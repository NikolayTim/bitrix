<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="bx_page">
    <div>
        <h2><?=GetMessage("TITLE");?></h2>

        <?if(count($arResult["CARDS"]) > 0):?>
            <table class="table">
				<thead style="background: #fc0">
					<tr>
						<td><?=GetMessage("DESC_CARDS");?></td>
						<td><?=GetMessage("DESC_BONUSES");?></td> 
					</tr> 
				</thead>			
                <tbody>
                    <?foreach($arResult["CARDS"] as $item):?>
                        <tr>
                            <td><b><?=$item["NUMBER"]?></b></td>
                            <td><b><?=$item["BONUSES"]?></b></td>
                        </tr>
                    <?endforeach;?>
                </tbody>
            </table>
        <?else:?>
            <p><?=GetMessage("CARDS_NOT_FOUND");?></p>
        <?endif;?>
    </div>

    <input type="button" class="btn btn-primary" id = "add_card" value="<?=GetMessage("ADD_CARD");?>">
    <input type="button" class="btn btn-primary" id = "view_bonus" value="<?=GetMessage("VIEW_BONUSES");?>">

    <div id="card" style="margin-top:20px;">
    </div>
</div>
