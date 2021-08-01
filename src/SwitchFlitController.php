<?php

namespace Cheddam\SwitchFlit;

use Cheddam\SwitchFlit\SwitchFlitable;
use Cheddam\SwitchFlit\WithCustomQuery;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\ORM\DataObject;

/**
 * SwitchFlitController
 * ---
 * @todo fix vue bug where using keyboard down you can scroll past the limited 5 results. Should bounce back to top like alfred
 * @todo theme to suit a standard ss4 CMS
 */
class SwitchFlitController extends Controller
{
    private static $url_handlers = [
        '$DataObject/records' => 'getRecordsForDataObject'
    ];

    private static $allowed_actions = [
        'getRecordsForDataObject',
        'index' => '->denyIndex'
    ];
    
    /**
     * denyIndex
     * ---
     * Convoluted way to deny access to everyone including escalated users,
     * Silverstripe does a permission check before checking falsy and 'index' => false will been seen as a permission code
     * 
     * @return false
     */
    public function denyIndex() {
        return false;
    }

    /**
     * Pulls all items from a SwitchFlitable DataObject and returns them as JSON.
     *
     * @param HTTPRequest $request The current request.
     * @return string The data in JSON format.
     *
     * @todo Clean up response handling.
     * @todo Allow custom columns? Pagination considerations?
     */
    public function getRecordsForDataObject(HTTPRequest $request)
    {
        $dataobject = urldecode($request->param('DataObject'));

        if (! class_exists($dataobject)) {
            return $this->sendError('The class ' . $dataobject . ' does not exist.');
        }

        if (! in_array(DataObject::class, class_parents($dataobject))) {
            return $this->sendError('The class ' . $dataobject . ' is not a DataObject.');
        }

        if (! in_array(SwitchFlitable::class, class_implements($dataobject))) {
            return $this->sendError('The class ' . $dataobject . ' is not SwitchFlitable.');
        }

        $records = $dataobject::get();

        if (in_array(WithCustomQuery::class, class_implements($dataobject))) {
            $records = $dataobject::SwitchFlitQuery($records);
        }

        $data = [];

        $results = $this->prepareRecords($records);

        $response = $this->getResponse();

        $response->setStatusCode(200);
        $response->addHeader('Content-Type', 'application/json');
        $response->setBody(json_encode(['items' => $results]));

        return $response;
    }

    public function prepareRecords($records)
    {
        $data = [];

        foreach ($records as $record) {
            if (! $record->canView()) continue;

            $data[] = [
                'title' => $record->SwitchFlitTitle(),
                'link' => $record->SwitchFlitLink()
            ];
        }

        return $data;
    }

    private function sendError($error)
    {
        $response = $this->getResponse();

        $response->setStatusCode(400);
        $response->addHeader('Content-Type', 'application/json');
        $response->setBody(json_encode([
            'errors' => [$error]
        ]));

        return $response;
    }
}
