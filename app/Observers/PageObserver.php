<?php

namespace App\Observers;

use App\Models\Page;

class PageObserver
{
    /**
     * Handle the Page "deleting" event.
     * Wird bei soft delete und force delete aufgerufen.
     */
    public function deleting(Page $page): void
    {
        // Bei soft delete: Images behalten (können mit Page wiederhergestellt werden)
        // Bei force delete: Images löschen
        if ($page->isForceDeleting()) {
            // Lösche alle zugehörigen Bilder inkl. Dateien
            foreach ($page->images as $image) {
                $image->delete(); // deleteFile() wird durch PageImage-Observer aufgerufen
            }
        }
    }

    /**
     * Handle the Page "restored" event.
     */
    public function restored(Page $page): void
    {
        // Wenn Page wiederhergestellt wird, sind Images noch da (soft delete)
        // Keine Aktion erforderlich
    }

    /**
     * Handle the Page "force deleted" event.
     */
    public function forceDeleted(Page $page): void
    {
        // Images wurden bereits in deleting() gelöscht
    }
}

