<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fill missing slugs with temporary unique values.
     */
    public function up(): void
    {
        $this->fillStoreSubscriptionSlugs();
        $this->fillPublicServiceSlugs();
        $this->fillPublicSoftwareSlugs();
    }

    /**
     * Remove only the temporary slugs created by this migration.
     */
    public function down(): void
    {
        DB::table('store_subscriptions')
            ->where('ar_slug', 'like', 'اشتراك-تجريبي-%')
            ->update(['ar_slug' => null]);

        DB::table('store_subscriptions')
            ->where('fr_slug', 'like', 'abonnement-temporaire-%')
            ->update(['fr_slug' => null]);

        DB::table('store_subscriptions')
            ->where('en_slug', 'like', 'temporary-subscription-%')
            ->update(['en_slug' => null]);

        DB::table('public_services')
            ->where('ar_slug', 'like', 'خدمة-تجريبية-%')
            ->update(['ar_slug' => null]);

        DB::table('public_services')
            ->where('fr_slug', 'like', 'service-temporaire-%')
            ->update(['fr_slug' => null]);

        DB::table('public_services')
            ->where('en_slug', 'like', 'temporary-service-%')
            ->update(['en_slug' => null]);

        DB::table('public_software')
            ->where('ar_slug', 'like', 'برنامج-تجريبي-%')
            ->update(['ar_slug' => null]);

        DB::table('public_software')
            ->where('fr_slug', 'like', 'logiciel-temporaire-%')
            ->update(['fr_slug' => null]);

        DB::table('public_software')
            ->where('en_slug', 'like', 'temporary-software-%')
            ->update(['en_slug' => null]);
    }

    /**
     * Fill missing StoreSubscription slugs.
     */
    private function fillStoreSubscriptionSlugs(): void
    {
        DB::table('store_subscriptions')
            ->select([
                'id',
                'ar_slug',
                'fr_slug',
                'en_slug',
            ])
            ->where(function ($query) {
                $query
                    ->whereNull('ar_slug')
                    ->orWhereNull('fr_slug')
                    ->orWhereNull('en_slug');
            })
            ->orderBy('id')
            ->chunkById(100, function ($subscriptions) {
                foreach ($subscriptions as $subscription) {
                    $updates = [];

                    if ($subscription->ar_slug === null) {
                        $updates['ar_slug'] =
                            'اشتراك-تجريبي-' . $subscription->id;
                    }

                    if ($subscription->fr_slug === null) {
                        $updates['fr_slug'] =
                            'abonnement-temporaire-' . $subscription->id;
                    }

                    if ($subscription->en_slug === null) {
                        $updates['en_slug'] =
                            'temporary-subscription-' . $subscription->id;
                    }

                    if ($updates !== []) {
                        DB::table('store_subscriptions')
                            ->where('id', $subscription->id)
                            ->update($updates);
                    }
                }
            });
    }

    /**
     * Fill missing PublicService slugs.
     */
    private function fillPublicServiceSlugs(): void
    {
        DB::table('public_services')
            ->select([
                'id',
                'ar_slug',
                'fr_slug',
                'en_slug',
            ])
            ->where(function ($query) {
                $query
                    ->whereNull('ar_slug')
                    ->orWhereNull('fr_slug')
                    ->orWhereNull('en_slug');
            })
            ->orderBy('id')
            ->chunkById(100, function ($services) {
                foreach ($services as $service) {
                    $updates = [];

                    if ($service->ar_slug === null) {
                        $updates['ar_slug'] =
                            'خدمة-تجريبية-' . $service->id;
                    }

                    if ($service->fr_slug === null) {
                        $updates['fr_slug'] =
                            'service-temporaire-' . $service->id;
                    }

                    if ($service->en_slug === null) {
                        $updates['en_slug'] =
                            'temporary-service-' . $service->id;
                    }

                    if ($updates !== []) {
                        DB::table('public_services')
                            ->where('id', $service->id)
                            ->update($updates);
                    }
                }
            });
    }

    /**
     * Fill missing PublicSoftware slugs.
     */
    private function fillPublicSoftwareSlugs(): void
    {
        DB::table('public_software')
            ->select([
                'id',
                'ar_slug',
                'fr_slug',
                'en_slug',
            ])
            ->where(function ($query) {
                $query
                    ->whereNull('ar_slug')
                    ->orWhereNull('fr_slug')
                    ->orWhereNull('en_slug');
            })
            ->orderBy('id')
            ->chunkById(100, function ($softwareItems) {
                foreach ($softwareItems as $software) {
                    $updates = [];

                    if ($software->ar_slug === null) {
                        $updates['ar_slug'] =
                            'برنامج-تجريبي-' . $software->id;
                    }

                    if ($software->fr_slug === null) {
                        $updates['fr_slug'] =
                            'logiciel-temporaire-' . $software->id;
                    }

                    if ($software->en_slug === null) {
                        $updates['en_slug'] =
                            'temporary-software-' . $software->id;
                    }

                    if ($updates !== []) {
                        DB::table('public_software')
                            ->where('id', $software->id)
                            ->update($updates);
                    }
                }
            });
    }
};

