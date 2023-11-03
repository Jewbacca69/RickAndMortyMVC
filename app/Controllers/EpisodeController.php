<?php

namespace App\Controllers;

use App\Api;
use Twig\Environment;

class EpisodeController
{
    private Api $api;
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->api = new Api();
        $this->twig = $twig;
    }

    public function index(): string
    {

        $episodes = $this->api->fetchEpisodes();

        $template = $this->twig->load('episodes.index.twig');

        return $template->render(['episodes' => $episodes]);
    }

    public function show(array $vars): string
    {
        $id = (int) $vars["id"];

        $episode = $this->api->fetchEpisode($id);
        $template = $this->twig->load('episode.show.twig');

        return $template->render(['episode' => $episode]);

    }

    public function search(): string
    {
        $input = $_GET["search"];
        $searchResults = $this->api->searchEpisodes($input);

        if (count($searchResults) === 1) {
            $id = $searchResults[0]->getId();
            header("Location: /episode/$id");
            exit;
        }

        $template = $this->twig->load('episode.search.twig');

        return $template->render(['episodes' => $searchResults, 'searchQuery' => $input]);
    }
}