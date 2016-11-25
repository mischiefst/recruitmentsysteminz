<?php
/**
 * SkillApplication
 * User: L
 * Date: 2016-09-13
 * Time: 19:52
 */
namespace Recruitment\Model;

class SkillApplication
{

    public $skill_idSkill;
    public $application_idApplication;
    public $knowledge;

    /**
     * @param $data
     */
    public function exchangeArray($data)
    {
        $this->skill_idSkill = (!empty($data['Skill_idSkill'])) ? $data['Skill_idSkill'] : null;
        $this->application_idApplication = (!empty($data['Application_idApplication'])) ? $data['Application_idApplication'] : null;
        $this->knowledge = (!empty($data['knowledge'])) ? $data['knowledge'] : 0;
    }
}