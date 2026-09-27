{{-- Summernote rich-text editor for every textarea.js-summernote on the page. --}}
@push('styles')
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.css" rel="stylesheet" />
@endpush

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (window.jQuery && $('.js-summernote').length) {
        $('.js-summernote').summernote({
          height: {{ $height ?? 360 }},
          placeholder: 'Write your content...',
          toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture', 'video', 'table']],
            ['view', ['fullscreen', 'codeview', 'help']]
          ]
        });
      }
    });
  </script>
@endpush
