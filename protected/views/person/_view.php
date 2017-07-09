<?php
/* @var $this PersonController */
/* @var $data Person */
?>

<div class="view">
<?php
foreach ( $spouse as $data->marriages1 )
{
    echo $spouse->namelink;
}

foreach ( $spouse as $data->marriages2 )
{
    echo $spouse->namelink;
}

foreach ( $spouse as $data->wives )
{
    echo $spouse->namelink;
}

foreach ( $spouse as $data->husbands )
{
    echo $spouse->namelink;
}

foreach ( $child as $data->children1 )
{
    echo $child->namelink;
}

foreach ( $child as $data->children2 )
{
    echo $child->namelink;
}
?>
	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo $data->namelink; ?>
	<br /> <b><?php echo CHtml::encode($data->getAttributeLabel('father_cid')); ?>:</b>
	<?php echo $data->father->namelink; ?>
	<br /> <b><?php echo CHtml::encode($data->getAttributeLabel('mother_cid')); ?>:</b>
	<?php echo $data->mother->namelink; ?>
	<br />
	
	<?php
	$spouses = array_merge($data->husbands,$data->wives );
	if(count($spouses)==1)
	{
	    ?><b><?php echo CHtml::encode($data->getAttributeLabel('spouse')); ?>:</b>
		<?php echo $spouses[0]->namelink; ?>
		<br />
		<?php 
	}
/*
 * <b><?php echo CHtml::encode($data->getAttributeLabel('gender'));
 * ?>:</b>
 * <?php echo CHtml::encode($data->gender); ?>
 * <br />
 *
 * <b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
 * <?php echo CHtml::encode($data->name); ?>
 * <br />
 *
 * <b><?php echo CHtml::encode($data->getAttributeLabel('dob')); ?>:</b>
 * <?php echo CHtml::encode($data->dob); ?>
 * <br />
 *
 * <b><?php echo CHtml::encode($data->getAttributeLabel('dod')); ?>:</b>
 * <?php echo CHtml::encode($data->dod); ?>
 * <br />
 *
 * <b><?php echo CHtml::encode($data->getAttributeLabel('bPics')); ?>:</b>
 * <?php echo CHtml::encode($data->bPics); ?>
 * <br />
 *
 * <b><?php echo CHtml::encode($data->getAttributeLabel('treepos'));
 * ?>:</b>
 * <?php echo CHtml::encode($data->treepos); ?>
 * <br />
 *
 * <b><?php echo CHtml::encode($data->getAttributeLabel('isDead'));
 * ?>:</b>
 * <?php echo CHtml::encode($data->isDead); ?>
 * <br />
 *
 * <b><?php echo CHtml::encode($data->getAttributeLabel('address'));
 * ?>:</b>
 * <?php echo CHtml::encode($data->address); ?>
 * <br />
 *
 * <b><?php echo CHtml::encode($data->getAttributeLabel('phone_mobile'));
 * ?>:</b>
 * <?php echo CHtml::encode($data->phone_mobile); ?>
 * <br />
 *
 * <b><?php echo CHtml::encode($data->getAttributeLabel('phone_res'));
 * ?>:</b>
 * <?php echo CHtml::encode($data->phone_res); ?>
 * <br />
 *
 * <b><?php echo CHtml::encode($data->getAttributeLabel('phone_off'));
 * ?>:</b>
 * <?php echo CHtml::encode($data->phone_off); ?>
 * <br />
 *
 * <b><?php echo CHtml::encode($data->getAttributeLabel('father_root'));
 * ?>:</b>
 * <?php echo CHtml::encode($data->father_root); ?>
 * <br />
 *
 * <b><?php echo CHtml::encode($data->getAttributeLabel('updated'));
 * ?>:</b>
 * <?php echo CHtml::encode($data->updated); ?>
 * <br />
 *
 */
?>

</div>

<?php
if (! $detailed)
    return;
if (count ( $data->father->children1 ))
{
    ?>
<div class="view">
	<h2><?php echo __('Siblings')?></h2>
	<ol>
<?php
foreach ( $data->father->children1 as $per )
    {
        if($per->cid == $data->cid)
            continue;
        
        echo CHtml::tag ( 'li', [ ], $per->namelink );
    }
    ?>
</ol>
</div>
<?php
}

if (count ( $data->husbands )>1 || count ( $data->wives )>1)
{
    ?>
<div class="view">
	<h2><?php echo __('Spouse') ?></h2>
	<ol>
<?php
foreach ( array_merge( $data->wives, $data->husbands) as $spouse )
    {
        echo CHtml::tag ( 'li', [ ], $spouse->namelink . ' ' . CHtml::link(__('create child'),['person/create','mother_cid' => $spouse->gender ? $data->cid : $spouse->cid ,'father_cid' => $spouse->gender ? $spouse->cid : $data->cid]));
    }
    ?>
</ol>
</div>
<?php
}

if (count ( $data->children ))
{
    ?>
<div class="view">
	<h2>Children</h2>
	<ol>
<?php
    foreach ( $data->children as $child )
    {
        echo CHtml::tag ( 'li', [ ], $child->namelink );
    }
    ?>
</ol>
</div>
<?php
}
?>