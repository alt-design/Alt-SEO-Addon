@extends('statamic::layout')

@section('content')
    <!-- Header Content -->
    <section>
        <h1 class="mb-2">Alt SEO</h1>
        <p>Default SEO settings, used as a fallback if not set on page.</p>
    </section>
    <!-- End Header Content -->

    <div>
        <publish-form
                action="{{ cp_route('alt-seo.update') }}"
                :blueprint='@json($blueprint)'
                :meta='@json($meta)'
                :values='@json($values)'
        ></publish-form>
    </div>
@endsection
