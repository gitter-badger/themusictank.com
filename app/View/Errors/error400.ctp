<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Errors
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>
<div class="header-wrapper">
    <section class="jumbotron introduction">
        <div class="container">
            <h1><?php echo __d('cake', '404'); ?></h1>
            <p class="lead"><?php
                printf(
                    __d('cake', 'The requested address %s was not found on this server.'),
                    "<strong>'{$url}'</strong>"
                );
            ?></p>
        </div>
    </section>
</div>

<div class="container container-fluid">
    <div class="row">
        <div class="md-col-12">
            <h2>Oops.</h2>
            <?php
            if (Configure::read('debug') > 0):
                echo $this->element('exception_stack_trace');
            endif;
            ?>
        </div>
    </div>

    <div class="row">
        <p style="font-size:8em; text-align:center;"><i class="fa fa-bug"></i></p>
    </div>
</div>
