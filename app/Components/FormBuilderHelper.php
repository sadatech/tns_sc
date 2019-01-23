<?php

namespace App\Components;

/**
 * 
 */
class FormBuilderHelper
{
	
	public static function defaultConfig()
	{
		return [
			// IF THE INPUT JUST NEED FORM INPUT WITHOUT LABEL AND CONTAINER
			'useLabel' => true,
			'boldLabel' => true,
			'labelClass' => '',

			// INFO TEXT UNDER INPUT FIELD
			'info' => null,

			// INFO TEXT TEMPLATE UNDER INPUT FIELD
			'infoTemplate' => '<span class="status-decline"><<field>></span>',

			// FORM ALIGNMENT
			'formAlignment' => 'vertical',

			// IF INPUT REQUIRED, THIS WILL BE SHOWN ON THE LABEL
			'requiredLabelText' => '<span class="status-decline">*</span>',

			// LABEL CONTAINER CLASS WHEN FORM ALIGNMENT IS VERTICAL
			'labelContainerClassVertical' => 'col-md-12',
			
			// INPUT CONTAINER CLASS WHEN FORM ALIGNMENT IS VERTICAL
			'inputContainerClassVertical' => 'col-md-12',

			// INPUT CONTAINER CLASS WHEN FORM ALIGNMENT IS HORIZONTAL
			'labelContainerClassHorizontal' => 'col-md-3',

			// INPUT CONTAINER CLASS WHEN FORM ALIGNMENT IS VERTICAL
			'inputContainerClassHorizontal' => 'col-md-9',

			/*
				ADDONS CONFIG
				addonsConfig => [
					'text' => ''
					'position' => '' -> left or right
				]
			*/ 
			'addons' => null,

			'htmlOptions' => null,

			// INPUT PROPERTIES
			'elOptions' => [
				'class' => 'form-control',
			]
		];
	}

	public static function setupDefaultConfig($name, $attributes)
	{
		$default = self::defaultConfig();
		$config = array_merge($default, $attributes);
		$config['elOptions'] = array_merge($default['elOptions'], $attributes['elOptions'] ?? []);

		// SETUP LABEL
		$config['textFormat'] = implode(' ', explode('_', $name));
		$config['labelText'] = $config['labelText'] ?? ucwords($config['textFormat']);
		$config['labelText'] = isset($config['elOptions']['required']) ? $config['labelText'] . ' ' . $config['requiredLabelText'] : $config['labelText'];
		$config['labelText'] = $config['boldLabel'] ? '<strong>' . $config['labelText'] . '</strong>' : $config['labelText'];

		// SETUP INFO
		if (!empty($config['info'])) {
			$config['info'] = str_replace('<<field>>', $config['info'], $config['infoTemplate']);
		}

		// SETUP FORM ALIGNMENT
		$config['labelContainerClass'] = $config['formAlignment'] === 'vertical' ? $config['labelContainerClassVertical'] : $config['labelContainerClass'] ?? $config['labelContainerClassHorizontal'];
		$config['inputContainerClass'] = $config['formAlignment'] === 'vertical' ? $config['inputContainerClassVertical'] : $config['inputContainerClass'] ?? $config['inputContainerClassHorizontal'];

		// SETUP ADDONS
		$config['addonsConfig'] = $config['addons'];

		// FOR ELEMENT PROPERTY
		$config['elOptions']['placeholder'] = $config['elOptions']['placeholder'] ?? 'Please enter ' . $config['textFormat']. ' here';

		// FOR FORMATING ARRAY elOptions INTO HTML ATTRIBUTES
		foreach ($config['elOptions'] as $attribute => $attributeValue) {
			$config['htmlOptions'] .= $attribute . '="' . $attributeValue . '" ';
		}

		return $config;
	}

	public static function arrayToHtmlAttribute(Array $elOptions) {
		$htmlAttributes = 'test ';
		foreach ($elOptions as $attribute => $attributeValue) {
			$htmlAttributes .= $attribute . '="' . $attributeValue . '" ';
		}
		return $htmlAttributes;
	}

}
