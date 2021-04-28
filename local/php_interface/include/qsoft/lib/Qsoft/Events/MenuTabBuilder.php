<?php

namespace Qsoft\Events;

use Bitrix\Main\GroupTable;

class MenuTabBuilder
{
    public static function handleEvent(&$form)
    {
        global $USER;

        if ($USER->IsAdmin()) {
            return;
        }

        $userGroupsID = $USER->GetUserGroupArray();

        $groupList = GroupTable::getList([
            'select' => ['ID', 'STRING_ID'],
            'filter' => ['=ID' => $userGroupsID],
        ]);

        while ($group = $groupList->fetch()) {
            if (!empty($group['STRING_ID'])) {
                if ($group['STRING_ID'] == 'seo') {
                    $form->SetShowSettings(false);

                    $oIblock = \CIBlock::GetList(
                        false,
                        ["CODE" =>
                            [
                                'CATALOG',
                                'refundNew',
                                'delivery',
                                'contacts',
                                'reserv',
                                'payment',
                                'events',
                                'blog',
                            ]
                        ]
                    );

                    while ($arIblock = $oIblock->Fetch()) {
                        $arIblocks[$arIblock['ID']] = $arIblock;
                    }
                    
                    $targerIblockID = str_replace(["form_element_", "form_section_"], "", $form->name);

                    if (isset($arIblocks[$targerIblockID])) {
                        foreach ($form->tabs as $num => $tab) {
                            if ($tab['TAB'] != 'SEO') {
                                if ($tab['TAB'] == 'Подробно' || $tab['TAB'] == 'Раздел') {
                                    foreach ($form->tabs[$num]['FIELDS'] as $key => $field) {
                                        if (in_array($field['id'], ['DESCRIPTION', 'DETAIL_TEXT'])) {
                                            $descriptionField[$key] = $field;
                                            unset($form->tabs[$num]['FIELDS'][$key]);
                                        }
                                    }
                                }
                                $form->tabs[$num]['TITLE'] = '<script>$(\'#tab_cont_' . $tab['DIV'] . '\').css(\'display\', \'none\');</script>';
                            } else {
                                $numTabSEO = $num;
                            }
                        }

                        if (!empty($descriptionField)) {
                            $form->tabs[$numTabSEO]['FIELDS'] = array_merge($form->tabs[$numTabSEO]['FIELDS'], $descriptionField);
                        }

                        array_unshift($form->tabs, $form->tabs[$numTabSEO]);
                        unset($form->tabs[$numTabSEO + 1]);
                    }
                }
            }
        }
    }
}
