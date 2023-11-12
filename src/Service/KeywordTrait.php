<?php

namespace App\Service;

trait KeywordTrait
{

    public function getAdverb(): string {
        $adverbs = ['apt', 'amazing', 'angry', 'radiant', 'pretty', 'smart', 'cool', 'friendly', 'best', 'bold', 'busy', 'brave', 'calm', 'captivating', 'clever', 'cheerful', 'cute', 'eager', 'enchanted', 'educated', 'fair', 'fine', 'free'];
        $key = array_rand($adverbs);
        return ucfirst($adverbs[$key]);
    }

    public function getSize(): string {
        $sizes = ['big', 'tiny', 'small', 'great', 'huge', 'massive', 'little', 'grand', 'epic', 'slim', 'gigantic'];
        $key = array_rand($sizes);
        return ucfirst($sizes[$key]);
    }

    public function getAnimal(): string {
        $animals = ['alpaca', 'bear', 'crow', 'dolphin', 'duck', 'eagle', 'ferret', 'frog', 'gecko', 'giraffe', 'goose', 'guppy', 'hare', 'hawk', 'hornet', 'horse', 'jaguar'];
        $key = array_rand($animals);
        return ucfirst($animals[$key]);
    }

}