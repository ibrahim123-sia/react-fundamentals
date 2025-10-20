## What is useState?

useState is a React Hook that lets you **add state** to functional components.
it have two thing one  variable to store data and one is a function to updates its value

Example:
```jsx
import { useState } from "react";

function Counter() {
  const [count, setCount] = useState(0);

  return (
    <div>
      <h2>Count: {count}</h2>
      <button onClick={() => setCount(count + 1)}>Increase</button>
    </div>
  );
}


## How it works

useState(initialValue) returns two things:
    Current state value
    A function to update it
Updating state re-renders the component automatically
