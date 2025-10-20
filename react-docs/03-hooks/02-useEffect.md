# useEffect Hook

useEffect: Handles side effects like fetching data, timers, event listeners, etc.

## 🧩 What is a Side Effect?

A **side effect** is anything that happens outside React’s rendering process — for example:

- Fetching data
- Setting timers
- Updating the DOM manually

## 🔧 Example: Basic useEffect

```jsx
import { useState, useEffect } from "react";

function Example() {
  const [count, setCount] = useState(0);

  useEffect(() => {
    document.title = `Count: ${count}`;
  }, [count]);

  return <button onClick={() => setCount(count + 1)}>Click {count}</button>;
}
```

Explanation:

The first argument is a function (your side effect).
The second argument [count] is a dependency array — it tells React when to re-run the effect.
If you leave it empty [], it runs only once (on mount).