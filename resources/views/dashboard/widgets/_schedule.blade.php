<?php
/**
 * @var \App\Models\Campaign $campaign
 * @var \App\Models\CampaignDashboardWidget $widget
 */
$entity = $widget->entity;
if (empty($entity) || empty($entity->child) || $entity->child->missingDetails()) {
    return;
}
?>
<x-box padding="0" class="widget-schedule {{ $widget->customClass($campaign) }}" id="dashboard-widget-{{ $widget->id }}">
    <livewire:widgets.schedule-widget
        :campaign="$campaign"
        :widget="$widget" />
</x-box>
