<?php

namespace CatchDesign\EmbedableGallery\Gridfield;

use SilverStripe\Versioned\VersionedGridFieldItemRequest as SS_VersionedGridFieldItemRequest;
use SilverStripe\Core\Config\Config;

class VersionedGridFieldItemRequest extends SS_VersionedGridFieldItemRequest {

    /**
     * Loads the given form data into the underlying dataobject and relation
     *
     * @param array $data
     * @param Form $form
     * @throws ValidationException On error
     * @return DataObject Saved record
     */
    public function saveFormIntoRecord($data, $form)
    {
        // Lifted from GridFieldDetailForm_ItemRequest
        $list = $this->gridField->getList();

        // Check object matches the correct classname
        if (isset($data['ClassName']) && $data['ClassName'] != $this->record->ClassName) {
            $newClassName = $data['ClassName'];
            // The records originally saved attribute was overwritten by $form->saveInto($record) before.
            // This is necessary for newClassInstance() to work as expected, and trigger change detection
            // on the ClassName attribute
            $this->record->setClassName($this->record->ClassName);
            // Replace $record with a new instance
            $this->record = $this->record->newClassInstance($newClassName);
        }

        // --- New code START ---

        // init the write components config
        $writeComponents = true;
        $recordClass = get_class($this->record);
        $writeComponentsConf = null;

        // try to load it from the model
        try { $writeComponentsConf = $this->record->getWriteComponentsConf(); } catch (\Exception $e) { /* do nothing */ }

        // try to up[date it from extensions
        $this->record->extend('updateWriteComponentsConf', $writeComponentsConf);

        // if there's something there use it
        if ($writeComponentsConf || $writeComponentsConf === false) {
            $writeComponents = $writeComponentsConf;
        }

        // --- New code END ---

        // Save form and any extra saved data into this dataobject.
        // Set writeComponents to $writeComponents
        //  -> was true for some reason which causes cascading writes and timeouts if a linked image asset has ~100+ sibling files
        $form->saveInto($this->record);
        $this->record->write(false, false, false, $writeComponents);
        $this->extend('onAfterSave', $this->record);

        $extraData = $this->getExtraSavedData($this->record, $list);
        $list->add($this->record, $extraData);

        // GridFieldDetailForm_ItemRequest returns here
        $record = $this->record;

        // Lifted from VersionedGridFieldItemRequest
        // Note: Don't publish if versioned, since that's a separate action
        $ownerIsVersioned = $record && $record->hasExtension(Versioned::class);
        $ownerIsPublishable = $record && $record->hasExtension(RecursivePublishable::class);
        if ($ownerIsPublishable && !$ownerIsVersioned) {
            /** @var RecursivePublishable $record */
            $record->publishRecursive();
        }

        return $record;
    }

}
