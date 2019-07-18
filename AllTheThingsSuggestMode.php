<?php

namespace Statamic\Addons\AllTheThings;

use Statamic\API\Page;
use Statamic\API\Entry;
use Illuminate\Support\Str;
use Statamic\Addons\Suggest\Modes\AbstractMode;

/**
 * A combination of pages and collection suggest modes.
 * Code mostly stolen from core.
 */
class AllTheThingsSuggestMode extends AbstractMode
{
    public function suggestions()
    {
        $suggestions = [];

        // Add pages to suggestions.
        // ---------------------------------------------------------------------
        // If a parent has been specified, get it's child pages at
        // the specified depth. Otherwise, just get all pages.
        if ($parent = $this->request->input('parent')) {
            $parent = (Str::startsWith($parent, '/')) ? Page::whereUri($parent) : Page::find($parent);
            $pages = $parent->children($this->request->input('depth'));
        } else {
            $pages = Page::all();
        }

        $pages = $pages->multisort($this->request->input('sort', 'title:asc'));

        foreach ($pages as $page) {
            $suggestions[] = [
                'value' => $page->id(),
                'text'  => $this->label($page, 'title')
            ];
        }




        // Add entries to suggestions.
        // ---------------------------------------------------------------------
        $collection = $this->request->input('collection');
        // If no specified collection, just get all entries.
        $entries    = $collection ? Entry::whereCollection($collection) : Entry::all();
        $entries    = $entries->multisort($this->request->input('sort', 'title:asc'));

        foreach ($entries as $entry) {
            $suggestions[] = [
                'value' => $entry->id(),
                'text'  => $this->label($entry, 'title')
            ];
        }

        return $suggestions;
    }
}
