<?php
/**
 * Markdown Combo Page
 * This page type allows for editing pages in both Markdown and ordinary HTML (Through Silverstripe's TinyMCE)
 *
 * TODO: Javascript that auomatically changes `ContentFormat` based on the selected tab.
 *
 * @author  Anselm Christophersen <ac@title.dk>
 */
class MarkdownComboPage extends SiteTree {

	public static $db = array(
		"ContentFormat" => "Enum('Markdown, HTML', 'Markdown')",
		"MarkdownContent" => "Text"
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		
		//Content Format Dropdown		
		$fields->addFieldToTab("Root.Main",
			new DropdownField("ContentFormat", "Content Format",
				singleton('Page')->dbObject('ContentFormat')->enumValues()
			),
			"Metadata"
		);		
		
		
		//Markdown & HTML Tabs
		$fields->removeByName("Content");
		$tabs = new TabSet("Root",
			$markdownContent = new Tab('Markdown',
				$markdownField = new MarkdownField('MarkdownContent', 'MarkdownContent')
			),
			$normalContent = new Tab('HTML',
				$htmlField = new HtmlEditorField("Content", _t('SiteTree.HTMLEDITORTITLE', "Content", 'HTML editor title'))
			)
		);
		
		//Adding the "stacked" class for giving more space
		$htmlField->addExtraClass('stacked');
		$markdownField->addExtraClass('stacked');
		$fields->addFieldToTab('Root.Main', $tabs, "Metadata");
		
		return $fields;
	}


}
class MarkdownComboPage_Controller extends ContentController {

	public function init() {
		parent::init();
	}

	/**
	 * Returns either HTML or Markdown Content, based on preferences
	 */
	function Content(){
		if ($this->ContentFormat == "Markdown") {
			return $this->dataRecord->obj("MarkdownContent")->Parse("MarkdownParser");
		} else {
			return $this->dataRecord->obj("Content");
		}
	}

}