<?php

namespace Icinga\Module\Monitoringaddons\ProvidedHook\Monitoring;

use gipfl\Translation\TranslationHelper;
use gipfl\Web\Table\NameValueTable;
use Icinga\Application\Config;
use Icinga\Module\Monitoring\Hook\DetailviewExtensionHook;
use Icinga\Module\Monitoring\Object\MonitoredObject;
use Icinga\Module\Monitoring\Object\Service;
use ipl\Html\Html;
use ipl\Html\HtmlDocument;

class DetailviewExtension extends DetailviewExtensionHook
{
    use TranslationHelper;

    public function getHtmlForObject(MonitoredObject $object)
    {
        if ($object instanceof Service) {
            $host = $object->getHost();
            $host->fetch();
            $hostVars = (array) $host->customvars;
            $showHostVars = Config::module('monitoringaddons')->getSection('hostvars_on_services')->toArray();
            $show = [];
            foreach ($showHostVars as $varName => $label) {
                if (isset($hostVars[$varName])) {
                    $show[$label] = $hostVars[$varName];
                } else {
                    $show[$label] = Html::tag('i', $this->translate('(not set)'));
                }
            }

            if (! empty($show)) {
                return (new HtmlDocument())->add([
                    Html::tag('h2', $this->translate('Host Properties')),
                    NameValueTable::create($show)
                ]);
            }
        }

        return null;
    }
}
