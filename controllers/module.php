<?php

class ModuleController extends PluginController
{

    public function show_action($parent_id = null)
    {
        $this->selected    = null;
        if ($parent_id === "root") {
            $studiengaenge = Studiengang::findBySQL("`stat` = 'genehmigt' ORDER BY name ASC");
            $areas = [];
            foreach ($studiengaenge as $studiengang) {
                $areas[] = [
                    'id' => "studiengang_".$studiengang->id,
                    'name' => $studiengang['name'],
                    'children' => count($studiengang->studiengangteile)
                ];
            }
            $this->areas = $areas;
            return;
        }
        list($type, $id) = explode("_", $parent_id);
        switch ($type) {
            case "studiengang":
                $parent = Studiengang::find($id);
                $this->parent = [
                    'id' => $parent_id,
                    'name' => $parent->getDisplayName(),
                    'parent_id' => 'root'
                ];
                $areas = [];
                foreach ($parent->studiengangteile as $studiengangteil) {
                    $areas[] = [
                        'id' => "studiengangteil_".$studiengangteil->id,
                        'name' => $studiengangteil->getDisplayName(),
                        'children' => count($studiengang->versionen)
                    ];
                }
                $this->areas = $areas;
                break;
            case "studiengangteil":
                $parent = StudiengangTeil::find($id);
                $this->parent = [
                    'id' => $parent_id,
                    'name' => $parent->getDisplayName(),
                    'parent_id' => 'studiengang_'.$parent->studiengang[0]->id
                ];
                $areas = [];
                foreach ($parent->versionen as $studiengangteilversion) {
                    $areas[] = [
                        'id' => "studiengangteilversion_".$studiengangteilversion->id,
                        'name' => $studiengangteilversion->getDisplayName(),
                        'children' => count($studiengang->abschnitte)
                    ];
                }
                $this->areas = $areas;
                break;
            case "studiengangteilabschnitt":
                $parent = StgteilAbschnitt::find($id);
                $this->parent = [
                    'id' => $parent_id,
                    'name' => $parent->getDisplayName(),
                    'parent_id' => 'studiengang_'.$parent->studiengang[0]->id
                ];
                $areas = [];
                foreach ($parent->module as $module) {
                    $areas[] = [
                        'id' => "modul_".$module->id,
                        'name' => $module->getDisplayName(),
                        'children' => 0
                    ];
                }
                $this->areas = $areas;
                break;
                break;
            case "modul":
                break;
        }
    }
}