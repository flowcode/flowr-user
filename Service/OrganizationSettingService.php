<?php

namespace Flower\UserBundle\Service;


class OrganizationSettingService
{
    private $organizationSettingRepository;

    public function __construct($organizationSettingRepository)
    {
        $this->organizationSettingRepository = $organizationSettingRepository;
    }


    /**
     * @param $settingName
     * @return mixed
     */
    public function getValue($settingName)
    {
        $setting = $this->organizationSettingRepository->findOneBy(array('name' => $settingName));
        if ($setting) {
            return $setting->getValue();
        }
        return null;
    }
}