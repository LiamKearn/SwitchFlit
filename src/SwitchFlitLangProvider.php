<?php

namespace Cheddam\SwitchFlit;

interface SwitchFlitLangProvider
{       
    /**
     * SearchPrompt
     *
     * @return string|null Search prompt or null to leave default.
     */
    public static function SearchPrompt();
}