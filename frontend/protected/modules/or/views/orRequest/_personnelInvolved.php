<!-- Surgeons -->
<?php $box = $this->beginWidget(
    'bootstrap.widgets.TbBox',
    array(
        'title' => 'Surgeons',
        'headerIcon' => 'icon-th-list',
        'htmlOptions'=>array('style'=>'width: 49%;display: inline-block;'),
        'headerButtons'=>array(
            array(
                'class'=>'bootstrap.widgets.TbButton',
                'label' => 'Add Surgeon',
                'size'=>'small',
                'type' => 'primary',
                'htmlOptions'=>array(
                    'data-toggle'=>'modal',
                    'data-target' => '#populate-personnel',
                    'data-purpose'=>'preop-surgeons',
                    'data-personnel-type'=>'S',
                    'style'=>'margin-right:5px'
                )
            )
        )
    )
);?>
<table id="preop-surgeons" class="items table table-striped">
    <thead>
    <tr>
        <td style="width:80px;">
            Personnel #
        </td>
        <td>
            Name
        </td>
    </tr>
    </thead>
    <tbody>
    <?php
        $counter = 0;
        if(isset($orRequest->orPreOpDetail->orSurgicalTeams))
        {
            foreach($orRequest->orPreOpDetail->orSurgicalTeams as $orSurgicalTeam)
            {
                if($orSurgicalTeam->role_type === 'S'){
                    $counter++;
                    echo "<tr>";
                    echo "<td>";
                    echo CHtml::hiddenField('OrPreOpDetails[orSurgicalTeams][personell_nr][]', $orSurgicalTeam->personnel->nr, array('id' => ''));
                    echo CHtml::hiddenField('OrPreOpDetails[orSurgicalTeams][role_type][]', "S", array('id' => ''));
                    echo $orSurgicalTeam->personnel->nr;
                    echo "</td>";
                    echo "<td>";
                    echo $orSurgicalTeam->personnel->person->fullName;
                    echo "<a class='pull-right btn btn-mini' onclick='javascript:deleteSched(this);' data-purpose='preop-surgeons'><i class='icon-remove'></i></a>";
                    echo "</td>";
                    echo "</tr>";
                }
            }
        }
        if($counter === 0)
        {
            echo "<tr id='default-entry'><td colspan='2'><i>No Surgeon Scheduled</i></td></tr>";
        }
    ?>
    </tbody>
</table>
<?php $this->endWidget(); ?>

<!-- Assisting Surgeons -->
<?php $box = $this->beginWidget(
    'bootstrap.widgets.TbBox',
    array(
        'title' => 'Assisting Surgeons',
        'headerIcon' => 'icon-th-list',
        'htmlOptions'=>array('style'=>'width: 49%;display: inline-block;float:right;'),
        'headerButtons'=>array(
            array(
                'class'=>'bootstrap.widgets.TbButton',
                'label' => 'Add Assisting Surgeon',
                'size'=>'small',
                'type' => 'primary',
                'htmlOptions'=>array(
                    'data-toggle'=>'modal',
                    'data-target' => '#populate-personnel',
                    'data-purpose'=>'preop-assisting-surgeons',
                    'data-personnel-type'=>'AS',
                    'style'=>'margin-right:5px'
                )
            )
        )
    )
);?>
<table id="preop-assisting-surgeons" class="items table table-striped">
    <thead>
    <tr>
        <td style="width:80px;">
            Personnel #
        </td>
        <td>
            Name
        </td>
    </tr>
    </thead>
    <tbody>
    <?php
    $counter = 0;
    if(isset($orRequest->orPreOpDetail->orSurgicalTeams))
    {
        foreach($orRequest->orPreOpDetail->orSurgicalTeams as $orSurgicalTeam)
        {
            if($orSurgicalTeam->role_type === 'AS'){
                $counter++;
                echo "<tr>";
                echo "<td>";
                echo CHtml::hiddenField('OrPreOpDetails[orSurgicalTeams][personell_nr][]', $orSurgicalTeam->personnel->nr, array('id' => ''));
                echo CHtml::hiddenField('OrPreOpDetails[orSurgicalTeams][role_type][]', "AS", array('id' => ''));
                echo $orSurgicalTeam->personnel->nr;
                echo "</td>";
                echo "<td>";
                echo $orSurgicalTeam->personnel->person->fullName;
                echo "<a class='pull-right btn btn-mini' onclick='javascript:deleteSched(this);' data-purpose='preop-assisting-surgeons'><i class='icon-remove'></i></a>";
                echo "</td>";
                echo "</tr>";
            }
        }
    }
    if($counter === 0)
    {
        echo "<tr id='default-entry'><td colspan='2'><i>No Assistant Surgeon Scheduled</i></td></tr>";
    }
    ?>
    </tbody>
</table>
<?php $this->endWidget(); ?>

<!-- Anesthesiologists -->
<?php $box = $this->beginWidget(
    'bootstrap.widgets.TbBox',
    array(
        'title' => 'Anesthesiologists',
        'headerIcon' => 'icon-th-list',
        'headerButtons'=>array(
            array(
                'class'=>'bootstrap.widgets.TbButton',
                'label' => 'Add Anesthesiologist',
                'size'=>'small',
                'type' => 'primary',
                'htmlOptions'=>array(
                    'data-toggle'=>'modal',
                    'data-target' => '#populate-personnel',
                    'data-purpose'=>'preop-anesthesiologists',
                    'data-personnel-type'=>'A',
                    'style'=>'margin-right:5px'
                )
            )
        )
    )
);?>
<table id="preop-anesthesiologists" class="items table table-striped">
    <thead>
    <tr>
        <td style="width:80px;">
            Personnel #
        </td>
        <td>
            Name
        </td>
    </tr>
    </thead>
    <tbody>
    <?php
    $counter = 0;
    if(isset($orRequest->orPreOpDetail->orSurgicalTeams))
    {
        foreach($orRequest->orPreOpDetail->orSurgicalTeams as $orSurgicalTeam)
        {
            if($orSurgicalTeam->role_type === 'A'){
                $counter++;
                echo "<tr>";
                echo "<td>";
                echo CHtml::hiddenField('OrPreOpDetails[orSurgicalTeams][personell_nr][]', $orSurgicalTeam->personnel->nr, array('id' => ''));
                echo CHtml::hiddenField('OrPreOpDetails[orSurgicalTeams][role_type][]', "A", array('id' => ''));
                echo $orSurgicalTeam->personnel->nr;
                echo "</td>";
                echo "<td>";
                echo $orSurgicalTeam->personnel->person->fullName;
                    echo "<a class='pull-right btn btn-mini' onclick='javascript:deleteSched(this);' data-purpose='preop-anesthesiologists'><i class='icon-remove'></i></a>";
                echo "</td>";
                echo "</tr>";
            }
        }
    }
    if($counter === 0)
    {
        echo "<tr id='default-entry'><td colspan='2'><i>No Anesthesiologist Scheduled</i></td></tr>";
    }
    ?>
    </tbody>
</table>
<?php $this->endWidget(); ?>

<!-- Scrub Nurses -->
<?php $box = $this->beginWidget(
    'bootstrap.widgets.TbBox',
    array(
        'title' => 'Scrub Nurses',
        'headerIcon' => 'icon-th-list',
        'htmlOptions'=>array('style'=>'width: 49%;display: inline-block;'),
        'headerButtons'=>array(
            array(
                'class'=>'bootstrap.widgets.TbButton',
                'label' => 'Add Scrub Nurse',
                'size'=>'small',
                'type' => 'primary',
                'htmlOptions'=>array(
                    'data-toggle'=>'modal',
                    'data-target' => '#populate-personnel',
                    'data-purpose'=>'preop-scrub-nurses',
                    'data-personnel-type'=>'SN',
                    'style'=>'margin-right:5px'
                )
            )
        )
    )
);?>
<table id="preop-scrub-nurses" class="items table table-striped">
    <thead>
    <tr>
        <td style="width:80px;">
            Personnel #
        </td>
        <td>
            Name
        </td>
    </tr>
    </thead>
    <tbody>
    <?php
        $counter = 0;
        if(isset($orRequest->orPreOpDetail->orSurgicalTeams))
        {
            foreach($orRequest->orPreOpDetail->orSurgicalTeams as $orSurgicalTeam)
            {
                if($orSurgicalTeam->role_type === 'SN'){
                    $counter++;
                    echo "<tr>";
                    echo "<td>";
                    echo CHtml::hiddenField('OrPreOpDetails[orSurgicalTeams][personell_nr][]', $orSurgicalTeam->personnel->nr, array('id' => ''));
                    echo CHtml::hiddenField('OrPreOpDetails[orSurgicalTeams][role_type][]', "SN", array('id' => ''));
                    echo $orSurgicalTeam->personnel->nr;
                    echo "</td>";
                    echo "<td>";
                    echo $orSurgicalTeam->personnel->person->fullName;
                    echo "<a class='pull-right btn btn-mini' onclick='javascript:deleteSched(this);' data-purpose='preop-scrub-nurses'><i class='icon-remove'></i></a>";
                    echo "</td>";
                    echo "</tr>";
                }
            }
        }
        if($counter === 0)
        {
            echo "<tr id='default-entry'><td colspan='2'><i>No Scrub Nurse Scheduled</i></td></tr>";
        }
    ?>
    </tbody>
</table>
<?php $this->endWidget(); ?>

<!-- Circulating Nurses -->
<?php $box = $this->beginWidget(
    'bootstrap.widgets.TbBox',
    array(
        'title' => 'Circulating Nurses',
        'headerIcon' => 'icon-th-list',
        'htmlOptions'=>array('style'=>'width: 49%;display: inline-block;float:right;'),
        'headerButtons'=>array(
            array(
                'class'=>'bootstrap.widgets.TbButton',
                'label' => 'Add Circulating Nurse',
                'size'=>'small',
                'type' => 'primary',
                'htmlOptions'=>array(
                    'data-toggle'=>'modal',
                    'data-target' => '#populate-personnel',
                    'data-purpose'=>'preop-circulating-nurses',
                    'data-personnel-type'=>'CN',
                    'style'=>'margin-right:5px'
                )
            )
        )
    )
);?>
<table id="preop-circulating-nurses" class="items table table-striped">
    <thead>
    <tr>
        <td style="width:80px;">
            Personnel #
        </td>
        <td>
            Name
        </td>
    </tr>
    </thead>
    <tbody>
    <?php
        $counter = 0;
        if(isset($orRequest->orPreOpDetail->orSurgicalTeams))
        {
            foreach($orRequest->orPreOpDetail->orSurgicalTeams as $orSurgicalTeam)
            {
                if($orSurgicalTeam->role_type === 'CN'){
                    $counter++;
                    echo "<tr>";
                    echo "<td>";
                    echo CHtml::hiddenField('OrPreOpDetails[orSurgicalTeams][personell_nr][]', $orSurgicalTeam->personnel->nr, array('id' => ''));
                    echo CHtml::hiddenField('OrPreOpDetails[orSurgicalTeams][role_type][]', "CN", array('id' => ''));
                    echo $orSurgicalTeam->personnel->nr;
                    echo "</td>";
                    echo "<td>";
                    echo $orSurgicalTeam->personnel->person->fullName;
                    echo "<a class='pull-right btn btn-mini' onclick='javascript:deleteSched(this);' data-purpose='preop-surgeons'><i class='icon-remove'></i></a>";
                    echo "</td>";
                    echo "</tr>";
                }
            }
        }
        if($counter === 0)
        {
            echo "<tr id='default-entry'><td colspan='2'><i>No Circulating Nurse Scheduled</i></td></tr>";
        }
    ?>
    </tbody>
</table>
<?php $this->endWidget(); ?>