<?php

use Livewire\Component;

new class extends Component {
    public $count = 0;

    public function increment()
    {
        $this->count++;
    }
};
?>

<div>
    <button type="button" wire:click="increment">Increment</button>

    <div>Count:{{ $count }}</div>
</div>

<script>
    console.log('Counter loaded');
</script>
