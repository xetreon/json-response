# Xetreon JSON Response

A lightweight Laravel package for **standardized JSON API responses**, validation, and error logging.  
Perfect for building **clean, consistent REST APIs** without repeating boilerplate code.

---

## 🚀 Features

- ✅ **Consistent JSON responses** (`success`, `error`, `validationError`)
- ✅ **Built-in validation helpers** with structured error output
- ✅ **Automatic error logging** with unique `trace_id` for easier debugging
- ✅ **Extensible BaseController** and **BaseException**
- ✅ **Publishable traits and base classes** for full customization
- ✅ **Configuration publishing** for easy overrides

---

## 📦 Installation

**Require the package via Composer:**

```bash
composer require xetreon/jsonresponse
```

**Publish the config file to customize defaults:**
```
php artisan vendor:publish --tag=config
```


## 🧩 Usage Example

Here’s how you can use `Xetreon\JsonResponse\Controllers\BaseController` in your controllers.

```php
use Xetreon\JsonResponse\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends BaseController
{
    public function index(Request $request)
    {
        $users = User::query();

        // fetchFromQuery automatically applies:
        // - status filtering (status[])
        // - sorting (sort_by & sort_order)
        // - pagination (per_page)
        return $this->fetchFromQuery($users, $request->all());
    }

    public function show($id)
    {
        return $this->fetchById(User::query(), $id, 'User not found');
    }
}
```

#### 📥 Sample Request JSON

```jsonc
{
  "status": ["active", "pending"],  // Filter results by status (uses whereIn)
  "sort_by": "created_at",          // Sort field
  "sort_order": "DESC",             // Sorting direction (ASC or DESC)
  "per_page": 15                    // Pagination size
}
```

#### 🖥 Example cURL Request

```bash
curl --location 'https://your-app.test/api/users' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--data '{
    "status": ["active", "pending"],
    "sort_by": "created_at",
    "sort_order": "DESC",
    "per_page": 15
}'
```
## ⚡ Direct Usage of Response Methods

You can use the built-in response helper methods in your controllers for quick and consistent JSON responses.

#### ✅ Success Response
```php
return $this->success(true, ['foo' => 'bar'], 'Operation successful');
```
```
return $this->error(false, [], 'Something went wrong', 500);
```

## 🛡️ Validation Helper

Use the built-in `validate()` method for consistent request validation and error responses.

#### Example Usage

```php
$result = $this->validate($request->all(), [
    'email' => 'required|email',
    'password' => 'required|min:8',
]);

if (!$result['result']) {
    return $this->validationError($result);
}
```

## 🛠 Publishing Base Classes and Traits

You can publish the `BaseController` and `BaseException` into your application for customization.

#### 🎛 Publish Classes
```bash
php artisan vendor:publish --tag=classes
```
#### 🎛 Publishing Traits

```bash
php artisan vendor:publish --tag=xetreon-jsonresponse-traits
```

## 📝 Using the LoggerTrait

The `LoggerTrait` provides an easy way to log **info**, **warning**, and **error** messages with consistent formatting and automatic file/line code references.

---

### 1️⃣ Importing & Using the Trait

```php
use Xetreon\JsonResponse\Traits\LoggerTrait;

class OrderService
{
    use LoggerTrait;

    public function processOrder($order)
    {
        // Log an informational message
        $this->createLog('Starting order processing', ['order_id' => $order->id]);

        try {
            // Your order processing logic...
        } catch (\Throwable $e) {
            // Log an error with context
            $this->createErrorLog('Order processing failed', [
                'order_id' => $order->id,
                'exception' => $e->getMessage()
            ]);
        }
    }
}
```

### 2️⃣ Available Methods

| Method              | Level   | Parameters                                             | Example                                                        |
|--------------------|--------|-------------------------------------------------------|----------------------------------------------------------------|
| `createLog()`       | Info   | `string $message, array $context = [], int $trace = 1` | `$this->createLog('User created', ['id' => 1]);`               |
| `createWarningLog()`| Warning| `string $message, array $context = [], int $trace = 1` | `$this->createWarningLog('User quota nearing limit');`         |
| `createErrorLog()`  | Error  | `string $message, array $context = [], int $trace = 1` | `$this->createErrorLog('Critical failure', ['error' => $e->getMessage()]);` |

---

**Parameter Details:**

- **`$message`** → A short, human-readable message.  
- **`$context`** → Additional data to log (automatically JSON-encoded).  
- **`$trace`** → *(Optional)* Stack trace depth — `1` for current method, `2` for caller, etc.  

---

### 3️⃣ Output Example (Log File)

When logging using `LoggerTrait`, you get standardized log entries:

```log
[2025-09-14 15:30:12] local.INFO: APP_COMMON - Starting order processing {"order_id":123}
[2025-09-14 15:30:13] local.ERROR: APP_COMMON - Order processing failed {"order_id":123,"exception":"Insufficient stock"}
```
### 4️⃣ Customizing the Log Channel

You can configure which channel `LoggerTrait` writes to by editing `config/xetreon-jsonresponse.php`:

```php
'log_channel' => env('XETREON_JSON_LOG_CHANNEL', config('logging.default')),
```
Or overwrite via `.env`
```
XETREON_JSON_LOG_CHANNEL=stack
```

### 5️⃣ Using Class Codes

You can map specific files to **short class codes** for more meaningful log identifiers.

### Example Configuration (`config/xetreon-jsonresponse.php`)

```php
'classcode' => [
    'app/Services/OrderService.php' => 'ORD',
    'app/Http/Controllers/UserController.php' => 'USR',
],
```

### ✅ Example Log Output
```
[2025-09-14 15:30:12] local.INFO: ORD:123 - Starting order processing {"order_id":123}
```
Here you will get the controller code : line number in the log.

### ✅ Why Use LoggerTrait?

- **Consistent:** Ensures the same log format across the entire application.  
- **Traceable:** Automatically adds file name and line number to every log entry.  
- **Configurable:** Lets you choose the log channel per environment.  
- **Structured:** Always logs with JSON-encoded context for easy parsing and analysis.


## ⚠️ Exception Usage

You can throw a `BaseException` anywhere in your application code to return a standardized JSON error response.

#### 💻 Example

```php
use Xetreon\JsonResponse\Exceptions\BaseException;

throw new BaseException('Something went wrong', 500, ['context' => 'extra-data']);
```
#### 📤 Example JSON Response

```json
{
  "result": false,
  "data": {
    "context": "extra-data"
  },
  "message": "Something went wrong",
  "status_code": 500,
  "error": {
    "type": "BaseException",
    "file": "app/Services/OrderService.php",
    "line": 42,
    "time": "2025-09-14 16:00:00",
    "code": "APP_COMMON"
  }
}
```
#### ✅ Highlights

- **`file` & `line`** – Useful in local/dev environments *(hidden in production for security)*.  
- **`code`** – Class code or module identifier for quick filtering in logs.  
- **`time`** – Exact timestamp when the exception was thrown.  

### ⚠️ Extending BaseException

You can create custom exceptions that extend `BaseException` for different error scenarios.

#### 💻 Example

```php
<?php

namespace App\Exceptions;

use Xetreon\JsonResponse\Exceptions\BaseException;

class AuthException extends BaseException
{
    public function __construct(string $message = 'Unable to Authenticate User', int $code = 401)
    {
        parent::__construct($message, $code);
    }
}
```
#### 📤 Example Usage

```php
use App\Exceptions\AuthException;

throw new AuthException();
```
### ⚠️ Adding Extra Data

You can pass extra data to the exception for better debugging and context.

### 💻 Example

```php
throw new AuthException('Token is invalid', 401, [
    'token' => $request->bearerToken(),
    'ip' => $request->ip()
]);
```
## 4️⃣ Class Codes (Optional)

You can define file-specific codes in `config/xetreon-jsonresponse.php` to make error codes more meaningful:

```php
'classcode' => [
    'app/Exceptions/AuthException.php' => 'AUTH',
    'app/Services/OrderService.php' => 'ORD'
],
```
Now your error JSON will include:

```json
"code": "AUTH:23"
```
Where 23 is the line number where the exception occurred.

### ✅ Benefits

- 🔥 **Consistent error format** — no more HTML errors in API responses.  
- 🧠 **Developer-friendly** — automatically adds `trace_id`, `file`, `line`, and `time`.  
- 🛠 **Extensible** — easily create your own custom exception classes.  
- 📊 **Debuggable** — logs all exceptions automatically for post-mortem analysis.








