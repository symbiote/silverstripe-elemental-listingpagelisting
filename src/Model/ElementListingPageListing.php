<?php

namespace Symbiote\Elemental\Model;

use SilverStripe\CMS\Controllers\CMSMain;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Security\Permission;
use Symbiote\ListingPage\ListingPage;
use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\ORM\FieldType\DBField;

class ElementListingPageListing extends BaseElement
{
    private static $table_name = 'ElementListingPageListing';

    private static $singular_name = 'listing block';

    private static $plural_name = 'listing blocks';

    private static $description = 'Listing for a Listing Page';

    private static $icon = 'font-icon-list';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        return $fields;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return _t(self::class . '.BlockType', 'Listing Page listing');
    }

    /**
     * @return string
     */
    public function getSummary()
    {
        return '';
    }

    /**
     * Generate the listing content.
     * {@link ListingPage->Content()} assumes the placeholder is in the $Content field,
     * so we need to temporarily replace the $Content value with the placeholder.
     *
     * @return HTMLText|null
     */
    public function getListing()
    {
        $page = $this->getPage();
        if (!$page || !($page instanceof \Symbiote\ListingPage\ListingPage)) {
            return;
        }

        $oldContent = $page->Content;
        $page->Content = '$Listing';
        $content = DBField::create_field('HTMLText', $page->Content());
        $page->Content = $oldContent;

        return $content;
    }

    /**
     * @return ValidationResult
     */
    public function validate()
    {
        $result = parent::validate();
        $page = $this->getPage();

        if (!($page instanceof \Symbiote\ListingPage\ListingPage)) {
            $result->addError('This block can only be added to a Listing Page');
        }

        return $result;
    }

    public function canCreate($member = null, $context = [])
    {
        if (!($controller = Controller::curr())
            || !$controller->hasMethod('currentPageID')
            || !($id = $controller->currentPageID())
            || !($page = SiteTree::get_by_id($id))
            || !($page instanceof \Symbiote\ListingPage\ListingPage)
        ) {
            return false;
        }

        return parent::canCreate($member, $context);
    }
}
