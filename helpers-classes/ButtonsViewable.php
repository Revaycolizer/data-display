<?php

namespace App\Helpers;

use App\Types\DataDisplayModes;
use App\Types\Buttons;

class ButtonsViewable
{
    /**
     * @param Buttons[] $buttons
     */
    public static function add(array $buttonsViewable)
    {
        if (in_array(Buttons::ADD, $buttonsViewable)) {
            return true;
        }

        return false;
    }

    /**
     * @param Buttons[] $buttons
     */
    public static function edit(array $buttonsViewable)
    {
        if (in_array(Buttons::EDIT, $buttonsViewable)) {
            return true;
        }

        return false;
    }

    /**
     * @param Buttons[] $buttons
     */
    public static function view(array $buttonsViewable)
    {
        if (in_array(Buttons::VIEW, $buttonsViewable)) {
            return true;
        }

        return false;
    }

    /**
     * @param Buttons[] $buttons
     */
    public static function delete(array $buttonsViewable)
    {
        if (in_array(Buttons::DELETE, $buttonsViewable)) {
            return true;
        }

        return false;
    }
}