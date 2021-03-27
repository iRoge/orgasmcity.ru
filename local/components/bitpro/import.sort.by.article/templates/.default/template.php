<?
$obCFile = new CFile();
?>
<?if(!empty($arResult["ERRORS"])):?>
	<div class="err-block">
		<? echo "<font>".implode("<br>",$arResult["ERRORS"])."</font><br />";?>
	</div>
<?endif;?>

<?if(empty($arResult['DATA'])):?>
	<?if(empty($arResult['TOTAL'])):?>
		Выберите файл для импорта:
		<form name="import_settings" method="post" enctype="multipart/form-data">
			<?=$obCFile->InputFile($arParams['IMPORT']['FILE_NAME'], 0, 0)?>
			<input type="hidden" name="config_import" value="Y" />
			<input type="submit" name="config" value="Далее" />
		</form>
	<?else:?>
		<h2>Результаты загрузки</h2>
		<div>Успешно загружено: <?=count($arResult['TOTAL'])?> </div>
		<hr/><br/><br/>
	<?endif;?>
<?else:?>
	<?if(!empty($arResult["ERRORS"])):?>
		<div class="err-message">ВНИМАНИЕ! Строки с ошибками не будут импортированы на сайт</div>
	<?endif;?>
	
	<form  name="import_start" method="post" enctype="multipart/form-data">
		<input type="hidden" name="start_import" value="Y" />
		<input type="hidden" name="tpm_file" value="<?=$arResult['TMP_FILE']?>" />
		<input type="hidden" name="file_hash" value="<?=$arResult['FILE_HASH']?>" />
		<input type="submit" value="Начать загрузку" />
		<input type="button" value="Отмена" onclick="window.location='likee_sort_import.php?lang=ru';" />
	</form>
	<br/><h2>Предварительный просмотр:</h2>
	Всего записей для импорта: <?=$arResult['ITEMS_COUNT']; ?><br/><br/>
	<table border=1>
		<tr>
			<th>ID</th>
			<th>Товар</th>
			<th>Артикул товара</th>
			<th>Текущая сортировка</th>
			<th>Новая сортировка</th>
		</tr>
	<?foreach ($arResult["ITEMS"] as $arData):?>
		<tr <?if($arData['ERROR'] == 'Y'):?> class="err-row" <?endif;?>>
			<td><?=$arData['ID']?></td>
			<td><?=$arData['ITEM']?></td>
			<td><?=$arData['ART']?></td>
			<td><?=$arData['SORT_CURR']?></td>
			<td><?=$arData['SORT_NEW']?></td>
		</tr>
	<?endforeach;?>
	</table> 
<?endif;?>