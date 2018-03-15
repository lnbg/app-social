<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ExampleTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $this->browse(function (Browser $browser) {
            $a = $browser->visit('https://www.facebook.com/mytamsinger1981')
                    ->type('email', 'ltnam2804@gmail.com')
                    ->type('pass', 'Hoaanhdao2804!')
                    ->click('#u_0_2')
                    ->visit('https://www.facebook.com/mytamsinger19812')
                    ->assertSee('Followers');
        });
    }
}
