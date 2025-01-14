<div class="space-y-4">
    <header>
        <h1 class="text-xl font-bold text-gray-900 md:text-2xl dark:text-white">
            {{ __('adminhub::settings.branches.create.title') }}
        </h1>
    </header>

    <form wire:submit.prevent="create"
          method="POST"
          class="space-y-4">
        @include('adminhub::partials.forms.branches.fields')
    </form>
</div>
