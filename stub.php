<?php

#[Employee]
class Gardener implements Passionate
{
    use Tools;

    public function workOn(Garden $garden, int|float $for = 7 /* in hours */)
    {
        if ($for == 0) {
            return 'Job done!';
        }

        $garden->water();
        $garden->fertilize();
        $garden->mow();

        return $this->workOn(garden: $garden, for: $for - 1);
    }
}
