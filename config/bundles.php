<?php

/**
 * SensioFrameworkExtraBundle it's no longer recommended as all the annotations provided byh that bundle are now
 * built-in in Symfony as PHP attributes:
 * https://symfony.com/bundles/SensioFrameworkExtraBundle/current/index.html
 * https://stackoverflow.com/questions/78617019/update-6-4-to-6-5-class-sensio-bundle-frameworkextrabundle-sensioframeworkextr
 */
return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Symfony\Bundle\MakerBundle\MakerBundle::class => ['dev' => true],
];
