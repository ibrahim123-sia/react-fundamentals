**Purpose:** Learn how to make **your own reusable hooks** for shared logic.

##  Why Custom Hooks?

When you notice you’re repeating logic (like fetching data, form validation, timers, etc.),  
you can move that logic into a **custom hook**.

## Example: useLocalStorage Hook

```jsx
import { useState, useEffect } from "react";
const [theme, setTheme] = useLocalStorage("theme", "light");

function useLocalStorage(key, initialValue) {
  const [value, setValue] = useState(() => {
    const stored = localStorage.getItem(key);
    return stored ? JSON.parse(stored) : initialValue;
  });

  useEffect(() => {
    localStorage.setItem(key, JSON.stringify(value));
  }, [key, value]);

  return [value, setValue];
}

export default useLocalStorage;
