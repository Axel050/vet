<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Dashboard')] class extends Component {
    //
};
?>

<div class="space-y-6">
    @if (auth()->user()->isSuperAdmin())
        <livewire:dashboard.superadmin />
    @else
        <livewire:dashboard.workshop />
    @endif
</div>
