<?php

namespace app;

use Carbon\Carbon;
use stdClass;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;
use App\Models\Episode;
use App\Models\Character;

class Api
{
    private HttpClientInterface $client;
    private const API_URL = "https://rickandmortyapi.com/api/episode";
    private const CHAR_URL = "https://rickandmortyapi.com/api/character/";

    public function __construct()
    {
        $this->client = HttpClient::create();
    }

    public function fetchEpisodes(): array
    {
        $episodes = [];
        $page = 1;

        while (true) {
            $response = self::fetchData(self::API_URL . "?page=$page");

            foreach ($response->results as $result) {
                $episodes[] = new Episode
                (
                    $result->id,
                    $result->name,
                    Carbon::parse($result->air_date),
                    $result->episode
                );
            }

            $page++;

            if ($response->info->next == null) {
                break;
            }
        }

        return $episodes;
    }

    public function fetchEpisode(int $id): Episode
    {
        $episodeData = self::fetchData(self::API_URL . "/$id");

        $episode = new Episode
        (
            $episodeData->id,
            $episodeData->name,
            Carbon::parse($episodeData->air_date),
            $episodeData->episode
        );

        foreach ($episodeData->characters as $characterUrl) {

            $explodedUrl = explode('/', $characterUrl);
            $characterId = end($explodedUrl);

            $characterData = $this->fetchCharacterById($characterId);

            $episode->addCharacter($characterData);
        }

        return $episode;
    }

    private function fetchCharacterById(int $id): Character
    {
        $characterUrl = self::CHAR_URL . "/$id";
        $explodedUrl = explode('/', $characterUrl);
        $characterId = end($explodedUrl);
        $characterData = self::fetchData($characterUrl);

        return new Character
        (
            $characterId,
            $characterData->name,
            $characterData->image,
            $characterData->species,
            $characterData->gender,
            $characterData->origin->name
        );
    }

    public function searchEpisodes(string $input): array
    {
        $episodes = $this->fetchEpisodes();

        $filteredEpisodes = array_filter($episodes, function (Episode $episode) use ($input) {
            return stripos($episode->getName(), $input) !== false
                || stripos($episode->getId(), $input) !== false
                || stripos($episode->getEpisode(), $input) !== false;
        });

        return array_values($filteredEpisodes);
    }

    private function fetchData(string $url): stdClass
    {
        $request = $this->client->request("GET", $url);
        return json_decode($request->getContent());
    }
}