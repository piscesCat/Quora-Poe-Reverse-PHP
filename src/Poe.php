<?php
/**
 * Author: Khai Phan
 *
 * GitHub: https://github.com/piscesCat/Quora-Poe-Reverse-PHP
 * 
 * Description: 
 * This is an unofficial composer library for seamless integration with Poe.com's Chatbot using PHP. 
 * The library provides convenient methods and functionalities to interact with the Chatbot, enabling developers to easily build 
 * It serves as a bridge between PHP and Poe.com's Chatbot API, offering a streamlined and efficient 
 * approach to harness the power of Poe.com's AI capabilities.
 * 
 * Please note that this library is not officially endorsed or maintained by Poe.com. Use it at your own discretion.
 */

namespace KhaiPhan\Quora;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Exception;

class Poe
{
    private $nextData;
    private $botName;
    private $botData;

    /**
     * Constructor for the Poe class.
     *
     * @param string $cookie_value The cookie value.
     * @param string $botName The bot name (default: 'Sage').
     * @param string|array|null $proxy
     * @throws Exception
     */
    public function __construct($cookie_value, $botName = "Sage", $proxy = null)
    {
        $headers = [
            "referer" => "https://poe.com/",
            "origin" => "https://poe.com",
            "user-agent" =>
                "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36",
            "content-type" => "application/json",
            "cookie" => "p-b=" . $cookie_value,
        ];
        $options = [
            "headers" => $headers,
            "timeout" => 100,
            "proxy" => $proxy,
        ];
        $this->client = new Client($options);
        $this->urlGql = "https://poe.com/api/gql_POST";
        $this->botName = $botName;
        $this->nextData = $this->getNextData();
        $this->botData = $this->getBotData();
    }

    /**
     * Get the answer from the bot for the given text.
     *
     * @param string $text The text to send to the bot.
     * @param bool $withChatBreak Whether to include chat breaks in the response (default: false).
     * @return string The answer from the bot.
     */
    public function getAnswer($text, $withChatBreak = false)
    {
        if ($this->sendMessage($text, $withChatBreak)) {
            return $this->getLatestMessage();
        }
    }

    /**
     * Send a message to the bot.
     *
     * @param string $text The text to send.
     * @param bool $withChatBreak Whether to include chat breaks in the message.
     * @return mixed The response data.
     * @throws Exception
     */
    public function sendMessage($text, $withChatBreak = false)
    {
        $chatId = $this->nextData->chatId;
        $bot = $this->botData->defaultBotObject->nickname;
        $variables = [
            "bot" => $bot,
            "chatId" => $chatId,
            "query" => $text,
            "source" => null,
            "withChatBreak" => $withChatBreak,
        ];
        $data = [
            "query" => $this->getGraphQl("sendMessageMutation"),
            "variables" => $variables,
        ];
        $responseData = $this->makeRequest($data);
        if (!isset($responseData["data"])) {
            throw new Exception(
                "Could not send message! Please retry, Data: " .
                    json_encode($responseData)
            );
        }

        return $responseData;
    }

    /**
     * Get the latest message from the bot.
     *
     * @return string The latest message from the bot.
     */
    private function getLatestMessage()
    {
        $bot = $this->botData->defaultBotObject->nickname;
        $data = [
            "operationName" => "ChatPaginationQuery",
            "query" => $this->getGraphQl("ChatPaginationQuery"),
            "variables" => [
                "before" => null,
                "bot" => $bot,
                "last" => 1,
            ],
        ];

        $authorNickname = "";
        $state = "incomplete";

        while (true) {
            sleep(2);

            $responseJson = $this->makeRequest($data);
            $edges =
                $responseJson["data"]["chatOfBot"]["messagesConnection"][
                    "edges"
                ];
            $lastEdge = end($edges);

            $text = $lastEdge["node"]["text"];
            $state = $lastEdge["node"]["state"];
            $authorNickname = $lastEdge["node"]["authorNickname"];

            if ($authorNickname === $bot && $state === "complete") {
                break;
            }
        }

        return $text;
    }

    /**
     * Get the next data from the website.
     *
     * @return mixed The next data.
     * @throws Exception
     */
    private function getNextData()
    {
        $url = "https://poe.com/{$this->botName}";
        $resp = $this->client->get($url);
        $html = $resp->getBody()->getContents();
        $pattern =
            '/<script id="__NEXT_DATA__" type="application\/json">(.*?)<\/script>/s';
        if (preg_match($pattern, $html, $matches)) {
            $nextData = json_decode($matches[1]);
            $nextData->chatId = $this->extractChatId($html);
            $nextData->formKey = $this->extractFormkey($html);
            return $nextData;
        } else {
            throw new Exception("Check your cookie value and bot name.");
        }
    }

    /**
     * Get the bot data from the website.
     *
     * @return mixed The bot data.
     */
    private function getBotData()
    {
        $url = "https://poe.com/_next/data/{$this->nextData->buildId}/{$this->botName}.json";

        $response = $this->client->get($url);

        $json = json_decode($response->getBody()->getContents());

        if (
            $json->pageProps->payload->chatOfBotDisplayName &&
            $json->pageProps->payload->chatOfBotDisplayName->chatId &&
            $json->pageProps->payload->chatOfBotDisplayName->id
        ) {
            $botNickName =
                $json->pageProps->payload->chatOfBotDisplayName
                    ->defaultBotObject->nickname;
            if ($displayName && $botNickName) {
                $this->bots[$botNickName] =
                    $json->pageProps->payload->chatOfBotDisplayName;
                $this->nicknames[$displayName] = $botNickName;
                $this->displayNames[$botNickName] = $displayName;
            }
            return $json->pageProps->payload->chatOfBotDisplayName;
        }
    }

    /**
     * Extract the chat ID from the HTML.
     *
     * @param string $html The HTML content.
     * @return string The extracted chat ID.
     * @throws Exception
     */
    private function extractChatId($html)
    {
        $regex = '/"chatId":([0-9]+)/';
        if (preg_match($regex, $html, $matches)) {
            return $matches[1];
        } else {
            throw new Exception("Can't get chatId");
        }
    }

    /**
     * Extract the form key from the HTML.
     *
     * @param string $html The HTML content.
     * @return string The extracted form key.
     * @throws Exception
     */
    private function extractFormkey($html)
    {
        $scriptRegex = "/<script>if\(.+\)throw new Error;(.+)<\/script>/";
        if (!preg_match($scriptRegex, $html, $scriptMatches)) {
            throw new Exception("Can't get poe-formkey");
        }
        $scriptText = $scriptMatches[1];

        $keyRegex = '/var .="([0-9a-f]+)",/';
        preg_match($keyRegex, $scriptText, $keyMatches);
        $keyText = $keyMatches[1];

        $cipherRegex = "/.\[(\d+)\]=.\[(\d+)\]/";
        preg_match_all(
            $cipherRegex,
            $scriptText,
            $cipherMatches,
            PREG_SET_ORDER
        );

        $cipherPairs = [];
        foreach ($cipherMatches as $match) {
            $cipherPair = [$match[1], $match[2]];
            $cipherPairs[] = $cipherPair;
        }

        $formkeyList = array_fill(0, count($cipherPairs), "");
        foreach ($cipherPairs as $pair) {
            $formkeyIndex = intval($pair[0]);
            $keyIndex = intval($pair[1]);
            $formkeyList[$formkeyIndex] = $keyText[$keyIndex];
        }

        $formkey = implode("", $formkeyList);
        return $formkey;
    }

    /**
     * Get the GraphQL query content.
     *
     * @param string $name The name of the query.
     * @return string The GraphQL query content.
     */
    private function getGraphQl($name)
    {
        return file_get_contents(__DIR__ . "/graphql/$name.graphql");
    }

    /**
     * Calculate the Poe tag ID based on the payload.
     *
     * @param mixed $payload The payload.
     * @return string The calculated Poe tag ID.
     */
    private function calculatePoeTagId($payload)
    {
        $payload = json_encode($payload);
        $base_string =
            $payload . $this->nextData->formKey . "WpuLMiXEKKE98j56k";
        return md5($base_string);
    }

    /**
     * Make a request to the Poe.com API.
     *
     * @param mixed $payload The payload for the request.
     * @return mixed The response data.
     * @throws Exception If there is no response from Poe.com.
     */
    private function makeRequest($payload)
    {
        $headers["content-type"] = "application/json";
        $headers["poe-formkey"] = $this->nextData->formKey;
        $headers["poe-tag-id"] = $this->calculatePoeTagId($payload);
        try {
            $response = $this->client->request("POST", $this->urlGql, [
                "headers" => $headers,
                "json" => $payload,
            ]);
            $responseBody = $response->getBody()->getContents();
            return json_decode($responseBody, true);
        } catch (GuzzleException $e) {
            throw new Exception("No response from Poe.com");
        }
    }
}
