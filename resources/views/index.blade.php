@extends('statamic::layout')

@section('content')
    <!-- Header Content -->
    <section>
        <h1 class="mb-2">Alt SEO</h1>
        <p>Default SEO settings, may be replaced per page in Collections > Pages > $page > Alt SEO tab</p>
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
