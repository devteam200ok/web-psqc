{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
   <url>
       <loc>{{ url('/') }}</loc>
   </url>
   <url>
       <loc>{{ url('/terms') }}</loc>
   </url>
   <url>
       <loc>{{ url('/privacy') }}</loc>
   </url>
   <url>
       <loc>{{ url('/request') }}</loc>
   </url>
   
   <!-- Performance routes -->
   <url>
       <loc>{{ url('/performance/speed') }}</loc>
   </url>
   <url>
       <loc>{{ url('/performance/load') }}</loc>
   </url>
   <url>
       <loc>{{ url('/performance/mobile') }}</loc>
   </url>
   
   <!-- Security routes -->
   <url>
       <loc>{{ url('/security/ssl') }}</loc>
   </url>
   <url>
       <loc>{{ url('/security/sslyze') }}</loc>
   </url>
   <url>
       <loc>{{ url('/security/header') }}</loc>
   </url>
   <url>
       <loc>{{ url('/security/scan') }}</loc>
   </url>
   <url>
       <loc>{{ url('/security/nuclei') }}</loc>
   </url>
   
   <!-- Quality routes -->
   <url>
       <loc>{{ url('/quality/lighthouse') }}</loc>
   </url>
   <url>
       <loc>{{ url('/quality/accessibility') }}</loc>
   </url>
   <url>
       <loc>{{ url('/quality/compatibility') }}</loc>
   </url>
   <url>
       <loc>{{ url('/quality/visual') }}</loc>
   </url>
   
   <!-- Content routes -->
   <url>
       <loc>{{ url('/content/links') }}</loc>
   </url>
   <url>
       <loc>{{ url('/content/structure') }}</loc>
   </url>
   <url>
       <loc>{{ url('/content/crawl') }}</loc>
   </url>
   <url>
       <loc>{{ url('/content/meta') }}</loc>
   </url>
</urlset>