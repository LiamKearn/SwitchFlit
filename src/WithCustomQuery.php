<?php

namespace Cheddam\SwitchFlit;

use SilverStripe\ORM\DataList;

interface WithCustomQuery
{
    /**
     * @param DataList $data The original DataList.
     * @return DataList The DataList with custom filters applied.
     */
    public static function SwitchFlitQuery(DataList $data);
}
