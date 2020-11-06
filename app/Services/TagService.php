<?php

namespace App\Services;

use App\Models\Tag;
use App\Taggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class TagService
{
    /**
     * @param Taggable $model
     * @param string[] $texts
     */
    public function attachTags(Taggable $model, array $texts) {
        $texts = array_wrap($texts);

        $tagsIds = [];
        foreach ($texts as $text) {
            $tags = $this->extractTags((string) $text);
            foreach ($tags as $tag) {
                $tag = Tag::createIfNotExists($tag);
                $tagsIds[] = $tag->id;
            }
        }

        $model->tags()->sync($tagsIds);
    }

    private function extractTags(string $text): Collection
    {
        $rawTags = collect(explode(' ', strip_tags($text)))->filter(function($word) {
            return mb_substr($word, 0, 1) == '#';
        })->map(function($word) {
            return mb_substr($word, 1);
        });

        $tagsFromMentions = $this->extractTagsFromMentions($text);

        return  $rawTags->merge($tagsFromMentions);
    }

    private function extractTagsFromMentions(string $text): Collection
    {
        $pattern = '<span class="mention" data-index="0" data-denotation-char="\#" data-id="\w+" data-value="(\w+)">﻿<span contenteditable="false"><span class="ql-mention-denotation-char">\#<\/span>\w+<\/span>﻿<\/span>';

        preg_match_all('/' . $pattern . '/', $text, $matches);

        if(empty($matches[1])) {
            return collect([]);
        }

        return collect($matches[1]);
    }
}