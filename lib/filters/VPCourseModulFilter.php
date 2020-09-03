<?php

class VPCourseModulFilter implements VPFilter
{
    static public function context()
    {
        return "courses";
    }

    /**
     * Name of the filter displayed in the configuration window.
     * @return string
     */
    public function getName()
    {
        return _("MVV Module");
    }

    /**
     * Name of the Parameter in URL and in widget.
     * @return string "modulteil_id"
     */
    public function getParameterName()
    {
        return "modul_id";
    }

    /**
     * Returns a widget (or null) that gets attached to the sidebar.
     * @return SidebarWidget
     */
    public function getSidebarWidget()
    {
        $modulwidget = new SelectWidget(
            _("Module"),
            PluginEngine::getURL("veranstaltungsplanung/planer/change_modul"),
            "modul_id"
        );
        $modulwidget->addLayoutCSSClass("courses");
        $modulwidget->addLayoutCSSClass("modulfilter");
        $module = [];
        if ($GLOBALS['user']->cfg->ADMIN_COURSES_STGTEIL) {
            $modulwidget->addElement(
                new SelectElement(
                    "",
                    _("Alle"),
                    false
                ),
                'select-'
            );
            $statement = DBManager::get()->prepare("
                SELECT mvv_modul.*
                FROM mvv_modul
                    INNER JOIN mvv_stgteilabschnitt_modul ON (mvv_stgteilabschnitt_modul.modul_id = mvv_modul.modul_id)
                    INNER JOIN mvv_stgteilabschnitt ON (mvv_stgteilabschnitt.abschnitt_id = mvv_stgteilabschnitt_modul.abschnitt_id)
                    INNER JOIN mvv_stgteilversion ON (mvv_stgteilversion.version_id = mvv_stgteilabschnitt.version_id)
                WHERE mvv_stgteilversion.stgteil_id = :stgteil_id
            ");
            $statement->execute([
                'stgteil_id' => Request::option("stgteil_id", $GLOBALS['user']->cfg->ADMIN_COURSES_STGTEIL)
            ]);
            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $data) {
                $module[] = Modul::buildExisting($data);
            }
        } else {
            $modulwidget->addElement(
                new SelectElement(
                    "",
                    _("Wählen Sie erst einen Studiengangteil aus"),
                    false
                ),
                'select-'
            );
        }
        foreach ($module as $modul) {
            $modulwidget->addElement(
                new SelectElement(
                    $modul->getId(),
                    $modul->getDisplayName(),
                    $modul->getId() === $GLOBALS['user']->cfg->ADMIN_COURSES_MODUL
                ),
                'select-'.$modul->getId()
            );
        }
        return $modulwidget;
    }

    public function applyFilter(\Veranstaltungsplanung\SQLQuery $query)
    {
        $GLOBALS['user']->cfg->store('ADMIN_COURSES_MODUL', Request::get("modul_id"));
        if (Request::get("modul_id")) {
            $query->join("mvv_lvgruppe_seminar", "`mvv_lvgruppe_seminar`.`seminar_id` = `seminare`.`Seminar_id`");
            $query->join("mvv_lvgruppe_modulteil", "`mvv_lvgruppe_modulteil`.`lvgruppe_id` = `mvv_lvgruppe_seminar`.`lvgruppe_id`");

            $query->where("modulteil_id", "`mvv_lvgruppe_modulteil`.`modulteil_id` = :modulteil_id", array(
                'modulteil_id' => Request::get("modulteil_id")
            ));
        }
    }
}