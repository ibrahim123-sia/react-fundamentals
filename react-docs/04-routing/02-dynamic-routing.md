## Purpose
Learn how to make **dynamic routes** — pages that depend on parameters (like `/post/:id`).


## 💡 What is Dynamic Routing?

Dynamic routes allow you to pass **parameters** (like IDs, usernames, slugs) through the URL.


Example:

- `/post/1`
- `/post/2`

---

## 🧩 Example

```jsx
// App.jsx
import { BrowserRouter, Routes, Route } from "react-router-dom";
import Posts from "./pages/Posts";
import PostDetails from "./pages/PostDetails";

function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/posts" element={<Posts />} />
        <Route path="/posts/:id" element={<PostDetails />} />
      </Routes>
    </BrowserRouter>
  );
}

export default App;
```
````

Key Concept
useParams() → hook that gives access to dynamic URL parts.