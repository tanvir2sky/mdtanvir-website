<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

/**
 * Starter articles for the blog. Posts are matched by slug and only created
 * if missing, so edits made in the admin panel are never overwritten.
 */
class PostSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->posts() as $post) {
            Post::query()->firstOrCreate(
                ['slug' => $post['slug']],
                $post + ['is_published' => true]
            );
        }
    }

    private function posts(): array
    {
        return [
            [
                'title' => 'Shipping LLM Features in Laravel Without the Surprises',
                'slug' => 'shipping-llm-features-in-laravel',
                'category' => 'AI',
                'tags' => ['llm', 'laravel', 'queues', 'production'],
                'is_featured' => true,
                'published_at' => now()->subDays(4)->setTime(9, 30),
                'excerpt' => 'Adding an LLM to a product is easy. Keeping it fast, affordable and predictable in production is the real work. Here is the playbook I use in Laravel.',
                'meta_description' => 'A practical playbook for shipping LLM-powered features in Laravel: queues, timeouts, structured output, cost control and observability.',
                'content' => <<<'HTML'
<p>Getting a demo working with a large language model takes an afternoon. Getting the same feature to behave well for real users, with real traffic and a real invoice at the end of the month, takes a lot more thought. Most of that work has nothing to do with prompts. It is ordinary backend engineering applied to a dependency that is slow, occasionally wrong, and billed per word.</p>
<p>These are the rules I follow when I put an LLM behind a Laravel feature.</p>

<h2>Treat the model like an unreliable network service</h2>
<p>An LLM call is an HTTP request to a third party that can take anywhere from one second to a minute, can be rate limited, and can fail. If you would not call a flaky payment provider inside a controller and make the user wait, do not do it with a model either.</p>
<p>That means three things from day one: explicit timeouts, retries only for errors that are worth retrying, and a plan for what the user sees when it fails.</p>

<h2>Move the call out of the request cycle</h2>
<p>For anything longer than a quick classification, dispatch a queued job and let the UI poll or listen for the result. The web request stays fast, a slow response never ties up a PHP worker, and you get retries and failure handling from the queue for free.</p>
<pre><code>class GenerateProductSummary implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = [10, 30, 90];
    public $timeout = 90;

    public function __construct(public Product $product) {}

    public function handle(LlmClient $llm): void
    {
        $summary = $llm->summarise($this->product->description);

        $this->product->update(['ai_summary' => $summary]);

        ProductSummaryReady::dispatch($this->product);
    }
}</code></pre>
<p>Remember that the queue's <code>retry_after</code> value must be longer than the job's <code>$timeout</code>, otherwise a slow model response can cause the same job to run twice.</p>

<h2>Be specific about timeouts and retries</h2>
<p>Laravel's HTTP client makes this easy. Retry on rate limits and server errors, but never on a 400: a bad request will be exactly as bad the second time.</p>
<pre><code>$response = Http::withToken(config('services.llm.key'))
    ->timeout(60)
    ->retry(3, 1000, function (Throwable $e) {
        return $e instanceof ConnectionException
            || ($e instanceof RequestException
                &amp;&amp; in_array($e->response->status(), [429, 500, 502, 503, 529]));
    })
    ->post(config('services.llm.url'), $payload);</code></pre>

<h2>Ask for structure, then validate it anyway</h2>
<p>If your code needs to act on the output, ask the model for JSON with a clear schema, and then validate it exactly as you would validate user input. Models are very good at following a format, but "very good" is not "always", and one malformed response should not break a page.</p>
<pre><code>$data = json_decode($text, true);

$validator = Validator::make($data ?? [], [
    'sentiment' => ['required', 'in:positive,neutral,negative'],
    'topics' => ['required', 'array', 'max:5'],
    'topics.*' => ['string', 'max:40'],
]);

if ($validator->fails()) {
    // Log it, fall back to a safe default, and move on.
}</code></pre>

<h2>Put a ceiling on cost</h2>
<p>Token usage grows quietly. A few habits keep it under control:</p>
<ul>
  <li><strong>Cap output length.</strong> Always set a maximum number of output tokens that fits the feature. A summary does not need four thousand tokens.</li>
  <li><strong>Cache identical work.</strong> If the same input produces an acceptable answer, cache it by a hash of the prompt and input. Regenerating the same product summary on every page view is money spent for nothing.</li>
  <li><strong>Rate limit per user.</strong> <code>RateLimiter::attempt()</code> with a daily window stops a single account, or a single bug, from running up the bill.</li>
  <li><strong>Send less context.</strong> Trim what you send to what the task needs. Bigger prompts are slower as well as more expensive.</li>
</ul>

<h2>Keep prompts in version control</h2>
<p>A prompt is code. Keep it in the repository, not in a database field someone edits on a Friday afternoon. Give it a version, review changes in pull requests, and keep a handful of real example inputs with known-good outputs so you can check a change before it ships.</p>
<div class="callout">A small set of saved examples you re-run after every prompt change catches most regressions long before a user does.</div>

<h2>Measure everything</h2>
<p>Log the model name, prompt version, input and output token counts, latency and whether the result passed validation. With those few fields you can answer the questions that always come up: why is this slow, why did the bill jump, and did last week's prompt change make things better or worse?</p>

<h2>Design the failure state first</h2>
<p>Decide what the user sees when the model is slow, down or returns something unusable: a placeholder, the non-AI version of the feature, or a clear "try again" message. Features that degrade gracefully earn trust. Features that spin forever lose it.</p>

<h2>Wrapping up</h2>
<p>None of this is exotic. Queues, timeouts, validation, caching and logging are tools every Laravel developer already uses. The trick is to apply them to the model from the start, instead of discovering the need for them after the first incident.</p>
HTML,
            ],

            [
                'title' => 'Laravel Queue Jobs That Survive Production',
                'slug' => 'laravel-queue-jobs-that-survive-production',
                'category' => 'Laravel',
                'tags' => ['queues', 'horizon', 'reliability'],
                'published_at' => now()->subDays(11)->setTime(10, 0),
                'excerpt' => 'Retries, timeouts, idempotency and uniqueness: the settings that decide whether your background jobs quietly work or quietly corrupt data.',
                'meta_description' => 'How to design Laravel queue jobs for production: idempotency, retries and backoff, timeouts, unique jobs, overlapping prevention and failure handling.',
                'content' => <<<'HTML'
<p>Queues are one of Laravel's best features, and one of the easiest to get subtly wrong. A job that works perfectly on your laptop can double-charge a customer in production because it ran twice, or silently give up because its timeout was shorter than the API it called. Here is the checklist I run through for every job that matters.</p>

<h2>Assume every job will run more than once</h2>
<p>Workers crash, deployments restart processes, and timeouts trigger retries. The queue guarantees a job runs <em>at least</em> once, not <em>exactly</em> once. So the most important property of a job is that running it twice is harmless.</p>
<p>In practice that means checking state before acting:</p>
<pre><code>public function handle(PaymentGateway $gateway): void
{
    if ($this->invoice->fresh()->paid_at) {
        return; // Already done on a previous attempt.
    }

    $gateway->charge($this->invoice, idempotencyKey: "invoice-{$this->invoice->id}");

    $this->invoice->update(['paid_at' => now()]);
}</code></pre>
<p>If the external API supports idempotency keys, use them. They protect you in the gap between "the charge succeeded" and "we saved that it succeeded".</p>

<h2>Configure retries and backoff on purpose</h2>
<p>The defaults are rarely what you want. Decide how many attempts make sense and how long to wait between them:</p>
<pre><code>public $tries = 5;

// Wait 10 seconds, then 1 minute, then 5 minutes between attempts.
public $backoff = [10, 60, 300];</code></pre>
<p>For jobs that depend on an external service, a time-based limit is often clearer than a count. Define <code>retryUntil()</code> and return a timestamp, and the job keeps retrying until then.</p>

<h2>Get timeouts right</h2>
<p>Two numbers need to agree. The job's <code>$timeout</code> is how long a worker lets it run. The connection's <code>retry_after</code> in <code>config/queue.php</code> is how long the queue waits before assuming the job died and handing it to another worker.</p>
<div class="callout"><code>retry_after</code> must always be a few seconds longer than your longest job timeout. If it is shorter, a slow job gets picked up by a second worker while the first is still running it.</div>

<h2>Prevent duplicates and overlaps</h2>
<p>Laravel gives you two different tools here, and it is worth knowing which is which.</p>
<h3>Unique jobs</h3>
<p>Implement <code>ShouldBeUnique</code> when only one copy of a job should be <em>queued</em> at a time, for example "rebuild the search index for this store":</p>
<pre><code>class RebuildStoreIndex implements ShouldQueue, ShouldBeUnique
{
    public $uniqueFor = 600;

    public function uniqueId(): string
    {
        return (string) $this->store->id;
    }
}</code></pre>
<h3>Without overlapping</h3>
<p>Use the <code>WithoutOverlapping</code> middleware when many jobs may be queued, but they must not <em>run</em> at the same time for the same resource:</p>
<pre><code>public function middleware(): array
{
    return [(new WithoutOverlapping($this->order->id))->releaseAfter(30)];
}</code></pre>

<h2>Dispatch after the transaction commits</h2>
<p>A classic bug: you create an order inside a database transaction, dispatch a job for it, and the job runs before the transaction commits. The job cannot find the order and fails. Dispatch with <code>afterCommit()</code>, or set <code>after_commit</code> to <code>true</code> on the queue connection, and Laravel waits until the data actually exists.</p>

<h2>Handle failure deliberately</h2>
<p>Every important job should have a <code>failed()</code> method. When all retries are exhausted, that is your chance to notify someone, mark the record so the UI can show it, or queue a compensating action. A failed job that nobody hears about is worse than one that never ran.</p>
<pre><code>public function failed(Throwable $exception): void
{
    $this->order->update(['sync_status' => 'failed']);

    Log::error('Order sync failed', [
        'order' => $this->order->id,
        'error' => $exception->getMessage(),
    ]);
}</code></pre>

<h2>Keep jobs small and payloads light</h2>
<p>Pass models, not large arrays. Laravel serialises only the model's identifier and re-fetches it when the job runs, so the job works with fresh data and the payload stays tiny. And prefer many small jobs to one enormous one: they retry independently and spread across workers. When you need to track a group of them, job batching gives you progress and a single completion callback.</p>

<h2>Watch the queue</h2>
<p>If you run Redis, Horizon gives you throughput, wait times and failed jobs at a glance, and lets you tune worker counts per queue. Whatever you use, alert on two things: jobs waiting longer than expected, and failure counts above zero for critical queues.</p>

<h2>The short version</h2>
<ul>
  <li>Make every job safe to run twice.</li>
  <li>Set tries, backoff and timeout explicitly, and keep <code>retry_after</code> larger than the timeout.</li>
  <li>Use unique jobs and overlap prevention where duplicates would hurt.</li>
  <li>Dispatch after commit, and always implement <code>failed()</code>.</li>
</ul>
HTML,
            ],

            [
                'title' => 'Shopify Webhooks in Laravel: Verify, Queue, Deduplicate',
                'slug' => 'shopify-webhooks-in-laravel',
                'category' => 'Shopify',
                'tags' => ['shopify', 'webhooks', 'security', 'laravel'],
                'published_at' => now()->subDays(19)->setTime(14, 15),
                'excerpt' => 'Webhooks are the backbone of most Shopify apps. Handle them in the wrong order and you get security holes, timeouts and duplicate orders.',
                'meta_description' => 'A reliable pattern for handling Shopify webhooks in Laravel: HMAC verification, fast responses with queues, deduplication and out-of-order events.',
                'content' => <<<'HTML'
<p>Almost every Shopify app lives on webhooks: orders created, products updated, apps uninstalled. They look simple, a POST request with some JSON, but a robust handler needs to deal with forged requests, tight response deadlines, duplicate deliveries and events that arrive out of order. This is the pattern I use in Laravel.</p>

<h2>1. Verify the HMAC before anything else</h2>
<p>Every webhook from Shopify includes an <code>X-Shopify-Hmac-Sha256</code> header: a base64-encoded HMAC-SHA256 of the raw request body, signed with your app's client secret. If you skip this check, anyone who finds your endpoint can send you fake orders.</p>
<p>Two details catch people out: you must hash the <strong>raw</strong> body, not re-encoded JSON, and you should compare with a constant-time function.</p>
<pre><code>class VerifyShopifyWebhook
{
    public function handle(Request $request, Closure $next)
    {
        $expected = base64_encode(hash_hmac(
            'sha256',
            $request->getContent(),
            config('services.shopify.secret'),
            true
        ));

        $given = (string) $request->header('X-Shopify-Hmac-Sha256');

        abort_unless(hash_equals($expected, $given), 401);

        return $next($request);
    }
}</code></pre>
<p>Webhook routes should also be excluded from CSRF protection, since Shopify obviously cannot send your CSRF token.</p>

<h2>2. Respond fast, work later</h2>
<p>Shopify expects your endpoint to respond within a few seconds. If it does not, the delivery counts as failed and Shopify retries it, and repeated failures can eventually get the subscription removed. So the controller should do almost nothing: verify, store or dispatch, and return a 200.</p>
<pre><code>public function __invoke(Request $request)
{
    ProcessShopifyWebhook::dispatch(
        topic: $request->header('X-Shopify-Topic'),
        shop: $request->header('X-Shopify-Shop-Domain'),
        webhookId: $request->header('X-Shopify-Webhook-Id'),
        payload: $request->json()->all(),
    );

    return response()->noContent();
}</code></pre>
<p>All the real work, such as calling the Admin API, updating records or sending emails, happens in the queued job, where it can take as long as it needs and retry safely.</p>

<h2>3. Expect duplicates</h2>
<p>Retries mean the same event can arrive more than once. Every delivery carries an <code>X-Shopify-Webhook-Id</code> header, which is the same across retries of one event. Record the IDs you have processed and skip ones you have already seen:</p>
<pre><code>public function handle(): void
{
    $firstTime = ProcessedWebhook::query()->insertOrIgnore([
        'webhook_id' => $this->webhookId,
        'created_at' => now(),
    ]);

    if ($firstTime === 0) {
        return; // Already handled.
    }

    // ... process the event
}</code></pre>
<p>A unique index on <code>webhook_id</code> makes this safe even when two workers pick up duplicates at the same moment.</p>

<h2>4. Do not trust the order of arrival</h2>
<p>Webhooks are not guaranteed to arrive in the order the events happened. An older <code>products/update</code> can land after a newer one. Before applying an update, compare the payload's <code>updated_at</code> with what you already have, and ignore anything older. When in doubt, treat the webhook as a signal and fetch the latest state from the Admin API.</p>

<h2>5. Handle the mandatory compliance webhooks</h2>
<p>Apps distributed through the Shopify App Store must handle the privacy compliance topics: <code>customers/data_request</code>, <code>customers/redact</code> and <code>shop/redact</code>. They are easy to forget until review. Route them through the same verified endpoint, and make sure the redact jobs actually delete or anonymise the data you hold.</p>
<div class="callout">Also subscribe to <code>app/uninstalled</code>. It is your signal to revoke stored access tokens and stop background work for that shop.</div>

<h2>6. Make it observable</h2>
<p>Log each webhook's topic, shop and ID, and how long processing took. When a merchant says "my order never synced", you want to answer with facts in two minutes, not a guess after two hours.</p>

<h2>Summary</h2>
<ul>
  <li>Verify the HMAC on the raw body with <code>hash_equals</code>.</li>
  <li>Return quickly and do the work in a queued job.</li>
  <li>Deduplicate using <code>X-Shopify-Webhook-Id</code> and a unique index.</li>
  <li>Guard against out-of-order events with <code>updated_at</code>.</li>
  <li>Implement the compliance topics and <code>app/uninstalled</code>.</li>
</ul>
HTML,
            ],

            [
                'title' => 'Thin Controllers in Laravel: Form Requests, Actions and DTOs',
                'slug' => 'thin-controllers-in-laravel',
                'category' => 'Architecture',
                'tags' => ['architecture', 'clean-code', 'laravel', 'testing'],
                'published_at' => now()->subDays(27)->setTime(11, 0),
                'excerpt' => 'When a controller method passes a hundred lines, it is doing someone else\'s job. A simple structure that keeps Laravel code easy to read, reuse and test.',
                'meta_description' => 'Keep Laravel controllers thin with Form Requests for validation, Action classes for business logic and DTOs for typed data. With examples.',
                'content' => <<<'HTML'
<p>Every Laravel project I have joined has at least one controller method that grew into a monster: validation, business rules, three API calls, some emails and a redirect, all in one place. It works, but nobody wants to touch it, and nothing inside it can be reused from a command, a job or an API endpoint.</p>
<p>The fix does not need a big architecture. Three small, boring building blocks go a long way.</p>

<h2>The controller's only job</h2>
<p>A controller should translate HTTP into a call to your application, and the result back into HTTP. That's it. If a method reads like a recipe for your business process, that recipe belongs somewhere else.</p>
<pre><code>class SubscriptionController extends Controller
{
    public function store(StartSubscriptionRequest $request, StartSubscription $startSubscription)
    {
        $subscription = $startSubscription->handle($request->toData());

        return redirect()
            ->route('subscriptions.show', $subscription)
            ->with('status', 'Your subscription is active.');
    }
}</code></pre>
<p>Five lines. You can read it in a few seconds, and the interesting logic is one click away.</p>

<h2>Form Requests own validation and authorisation</h2>
<p>A Form Request keeps the rules next to the authorisation check, and gives you a natural place to turn raw input into something typed:</p>
<pre><code>class StartSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Subscription::class);
    }

    public function rules(): array
    {
        return [
            'plan' => ['required', Rule::exists('plans', 'slug')],
            'seats' => ['required', 'integer', 'min:1', 'max:500'],
            'coupon' => ['nullable', 'string', 'max:40'],
        ];
    }

    public function toData(): SubscriptionData
    {
        return new SubscriptionData(
            user: $this->user(),
            plan: Plan::where('slug', $this->validated('plan'))->firstOrFail(),
            seats: (int) $this->validated('seats'),
            coupon: $this->validated('coupon'),
        );
    }
}</code></pre>

<h2>DTOs make the data explicit</h2>
<p>Passing <code>$request->all()</code> around means every function has to guess which keys exist. A small readonly class removes the guessing, and your editor can autocomplete it:</p>
<pre><code>final readonly class SubscriptionData
{
    public function __construct(
        public User $user,
        public Plan $plan,
        public int $seats,
        public ?string $coupon = null,
    ) {}
}</code></pre>
<p>It has no behaviour and no framework dependencies. It is just a typed bag of values that makes the code honest about what it needs.</p>

<h2>Actions hold the business logic</h2>
<p>An Action is a class with one public method that does one thing your application does. Name it after that thing:</p>
<pre><code>class StartSubscription
{
    public function __construct(private BillingGateway $billing) {}

    public function handle(SubscriptionData $data): Subscription
    {
        return DB::transaction(function () use ($data) {
            $subscription = $data->user->subscriptions()->create([
                'plan_id' => $data->plan->id,
                'seats' => $data->seats,
            ]);

            $this->billing->createSubscription($subscription, $data->coupon);

            SubscriptionStarted::dispatch($subscription);

            return $subscription;
        });
    }
}</code></pre>
<p>Because it only depends on a DTO, the same action works from a controller, an Artisan command, a queued job or an API route. Nothing about it knows it was triggered by a web form.</p>

<h2>Why this is easier to test</h2>
<p>You can test the action directly, without HTTP, by building a DTO and swapping the billing gateway for a fake. The controller test then only needs to check the wiring: valid input redirects, invalid input returns errors, and unauthorised users are blocked.</p>

<h2>Where to stop</h2>
<p>This is not a rule for every endpoint. A simple CRUD update that saves validated fields does not need an action and a DTO; that would just be ceremony. I reach for this structure when a controller method starts to:</p>
<ul>
  <li>coordinate more than one model or external service,</li>
  <li>need the same logic somewhere else, or</li>
  <li>become hard to test without making real HTTP requests.</li>
</ul>
<div class="callout">Start simple. Extract an action the second time you need the logic, or the first time the controller stops fitting on your screen.</div>
HTML,
            ],

            [
                'title' => 'Finding and Fixing N+1 Queries in Eloquent',
                'slug' => 'finding-and-fixing-n-plus-1-queries-in-eloquent',
                'category' => 'Performance',
                'tags' => ['eloquent', 'database', 'performance'],
                'published_at' => now()->subDays(36)->setTime(9, 0),
                'excerpt' => 'The most common reason a Laravel page gets slow is not PHP. It is the page quietly running two hundred queries. How to catch N+1 problems early and fix them properly.',
                'meta_description' => 'How to detect and fix N+1 query problems in Laravel Eloquent with eager loading, withCount, preventLazyLoading, chunking and indexes.',
                'content' => <<<'HTML'
<p>A page that loads in 80 milliseconds with ten records and three seconds with five hundred almost always has the same cause: an N+1 query. It is the most common performance problem in Laravel applications, and one of the easiest to prevent once you know what to look for.</p>

<h2>What an N+1 looks like</h2>
<p>Here is a completely innocent-looking Blade loop:</p>
<pre><code>@foreach ($orders as $order)
    {{ $order->customer->name }} - {{ $order->items->count() }} items
@endforeach</code></pre>
<p>One query loads the orders. Then, for <em>every</em> order, Eloquent lazily loads the customer and the items. With 100 orders that is 201 queries. Each one is fast, but the round trips add up, and the database does far more work than it needs to.</p>

<h2>Make lazy loading fail loudly</h2>
<p>The best fix is catching the problem before it ships. Laravel can throw an exception whenever a relationship is lazy loaded. Turn it on outside production in <code>AppServiceProvider</code>:</p>
<pre><code>public function boot(): void
{
    Model::preventLazyLoading(! $this->app->isProduction());
}</code></pre>
<p>Now the loop above fails in development and in your test suite, pointing at the exact relationship. If you want stricter defaults in general, <code>Model::shouldBeStrict()</code> also flags silently discarded attributes and access to attributes that were never loaded.</p>

<h2>Eager load what you use</h2>
<p>The fix is to load the relationships up front, in a fixed number of queries:</p>
<pre><code>$orders = Order::query()
    ->with('customer')
    ->withCount('items')
    ->latest()
    ->paginate(50);</code></pre>
<p>Three queries, whatever the page size. Note <code>withCount()</code>: if you only need the number of items, do not load every item just to count them. The count arrives as an <code>items_count</code> attribute.</p>
<h3>Nested and constrained loading</h3>
<p>You can eager load nested relationships with dot notation, and constrain what gets loaded:</p>
<pre><code>Order::with([
    'customer:id,name',
    'items' => fn ($query) => $query->where('refunded', false),
    'items.product',
])->get();</code></pre>
<p>When selecting specific columns on a relationship, always include the key Eloquent uses to match records (here <code>id</code>), otherwise the relationship silently comes back empty.</p>
<h3>Loading after the fact</h3>
<p>If you already have a collection, <code>load()</code> and <code>loadMissing()</code> eager load onto it. <code>loadMissing()</code> is handy in shared code because it skips relationships that are already there.</p>

<h2>Large datasets need a different approach</h2>
<p>For exports, reports and batch jobs, loading everything into memory is its own problem. Process records in chunks instead:</p>
<pre><code>Order::query()
    ->with('customer')
    ->where('status', 'shipped')
    ->chunkById(500, function ($orders) {
        foreach ($orders as $order) {
            // ...
        }
    });</code></pre>
<p>Prefer <code>chunkById()</code> or <code>lazyById()</code> over plain <code>chunk()</code> when you update records inside the loop: paging by offset while changing the rows you filter on can skip records.</p>

<h2>Do not forget indexes</h2>
<p>Eager loading reduces the <em>number</em> of queries. Indexes make each query fast. Foreign keys, columns you filter on, and columns you sort by are the usual candidates. Run the slow query through <code>EXPLAIN</code> and look for full table scans.</p>
<pre><code>$table->index(['status', 'created_at']);</code></pre>

<h2>See what is really happening</h2>
<p>You cannot fix what you cannot see. Use Laravel Debugbar or Telescope locally to see the query count per page, and consider logging a warning when a request runs more queries than you expect. Either way, make "how many queries does this page run?" a normal question in code review.</p>

<h2>Checklist</h2>
<ul>
  <li>Enable <code>preventLazyLoading()</code> outside production.</li>
  <li>Eager load with <code>with()</code>; count with <code>withCount()</code>.</li>
  <li>Select only the columns you need, keeping the keys.</li>
  <li>Use <code>chunkById()</code> or <code>lazyById()</code> for big datasets.</li>
  <li>Index what you filter and sort on, and check with <code>EXPLAIN</code>.</li>
</ul>
HTML,
            ],

            [
                'title' => 'Using AI Coding Assistants Without Lowering the Bar',
                'slug' => 'using-ai-coding-assistants-without-lowering-the-bar',
                'category' => 'Workflow',
                'tags' => ['ai', 'productivity', 'code-review', 'claude-code'],
                'published_at' => now()->subDays(45)->setTime(16, 0),
                'excerpt' => 'AI assistants like Claude Code and Copilot can make you much faster, or much sloppier. The habits that keep the speed and lose the sloppiness.',
                'meta_description' => 'Practical habits for using AI coding assistants such as Claude Code and GitHub Copilot while keeping code quality, security and ownership high.',
                'content' => <<<'HTML'
<p>AI coding assistants are now part of my daily work. I use them to explore unfamiliar code, draft tests, handle repetitive refactors and get a first version of things I already know how to build. Used well, they remove a lot of friction. Used carelessly, they let you ship code you do not understand, faster than ever.</p>
<p>These are the habits that keep me on the right side of that line.</p>

<h2>You still own every line</h2>
<p>The most important rule is the simplest: if it is in my pull request, it is my code. "The assistant wrote it" is not an explanation anyone accepts during an incident. So I review AI-written changes exactly as I would review a colleague's pull request: line by line, asking why, not just whether it runs.</p>

<h2>Plan before you generate</h2>
<p>The quality of the output tracks the quality of the request. Before asking for code, I spend a minute describing the change: which files are involved, what the existing pattern is, what must not change, and how we will know it works. For larger changes I ask the assistant to propose a plan first and only let it write code once the plan looks right.</p>
<div class="callout">A clear description of the goal and constraints saves more time than any clever prompt trick.</div>

<h2>Keep the changes small</h2>
<p>Generating a thousand-line change is easy. Reviewing it properly is not. I keep AI-assisted changes to a size I can actually read, and commit in steps, so each diff tells one story. Small steps also make it obvious where something went wrong.</p>

<h2>Let tests be the specification</h2>
<p>Tests are where assistants shine and where they are most useful as a safety net. Two patterns work well:</p>
<ul>
  <li><strong>Write or agree the tests first.</strong> Describe the behaviour, have the tests written, read them carefully, then let the implementation follow. The tests become the contract.</li>
  <li><strong>Always run them.</strong> An assistant confidently saying "this should work" is not evidence. A passing test suite, a successful build and a quick manual check are.</li>
</ul>

<h2>Match the codebase, not the internet</h2>
<p>Assistants default to the most common way of doing something, which is not always your project's way. Point them at an existing example ("do it like the other importers in this folder") and correct drift early: naming, error handling, how you structure services. Consistency matters more than any single clever solution.</p>

<h2>Where I slow down</h2>
<p>Some areas deserve extra care no matter who wrote the code:</p>
<ul>
  <li><strong>Security:</strong> authentication, authorisation, input validation, anything touching secrets or user data.</li>
  <li><strong>Data changes:</strong> migrations, deletions and anything that is hard to undo.</li>
  <li><strong>Money:</strong> payments, billing and invoicing logic.</li>
  <li><strong>New dependencies:</strong> check that a suggested package exists, is maintained and is actually needed.</li>
</ul>
<p>Here I read more slowly, test edge cases by hand, and sometimes simply write the critical part myself.</p>

<h2>Protect secrets and private code</h2>
<p>Know what your tools send where, and follow your company's policy. Never paste production credentials, customer data or keys into a prompt. Keep secrets in environment variables, and review generated config files before committing them.</p>

<h2>Use it to learn, not just to type</h2>
<p>Some of the best value comes from questions rather than code: "walk me through how this request reaches the database", "what could go wrong with this migration on a large table?", "what are the trade-offs between these two approaches?". Used this way, an assistant makes you a better engineer, not just a faster one.</p>

<h2>The balance</h2>
<p>The goal is not to write less code. It is to spend your attention where it matters most: understanding the problem, making good design decisions and verifying the result. Let the assistant take the typing, and keep the thinking for yourself.</p>
HTML,
            ],
        ];
    }
}
