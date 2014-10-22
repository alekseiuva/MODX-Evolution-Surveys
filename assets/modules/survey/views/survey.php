<?php defined('MODX_BASE_PATH') or die('Error'); ?>
<?php /** @var Survey $survey */ ?>
<?php /** @var Surveys $app */ ?>
<div class="survey__options">
    <?php foreach ($survey->options as $o): ?>
    <?php $t = $app->calculateRate($survey->votes, $o->votes) ?>
    <div class="survey__option">
        <div class="survey__option_title"><?= $o->title ?>: <span><?= $t ?>% (<?= $o->votes ?>)</span></div>
        <div class="survey__option_progress"><span style="width:<?= $t ?>%"></span></div>
    </div>
    <?php endforeach ?>
</div>
