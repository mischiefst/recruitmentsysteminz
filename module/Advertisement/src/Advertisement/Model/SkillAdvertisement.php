<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2016-09-13
 * Time: 19:52
 */
namespace Advertisement\Model;

class SkillAdvertisement
{

    public $skill_idSkill;
    public $advertisement_idAdvertisement;

    /**
     * @param $data
     */
    public function exchangeArray($data)
    {
        $this->skill_idSkill = (!empty($data['skill_idSkill'])) ? $data['skill_idSkill'] : null;
        $this->advertisement_idAdvertisement = (!empty($data['advertisement_idAdvertisement'])) ? $data['advertisement_idAdvertisement'] : null;
    }

}