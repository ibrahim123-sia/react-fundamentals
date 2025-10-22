Purpose

Learn how to use Context API to share data between deeply nested components without prop drilling.

## ⚙️ What is Context API?

Context API allows you to share **global data** (like theme, user, language, etc.) across your React app **without passing props manually** through every level.

## Steps to Use Context API

1. **Create Context**
2. **Wrap Provider**
3. **Consume Context**

---

## Example

```jsx
// ThemeContext.js
import { createContext } from "react";
export const ThemeContext = createContext();
