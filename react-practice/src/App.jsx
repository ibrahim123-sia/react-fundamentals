import React from "react";
import { Route, Routes, BrowserRouter } from "react-router-dom";
import Component from "../src/Practices/Component.jsx";
import Props from "../src/Practices/Props.jsx";
import EventHandling from "../src/Practices/EventHandling.jsx";
import ConditionalRendering from "../src/Practices/ConditionalRendering.jsx";
import Stopwatch from "../src/Practices/Stopwatch.jsx";
import WeatherFetcher from "../src/Practices/weatherFetcher.jsx";
import ReactRouter from "../src/Practices/ReactRouter.jsx";
import DynamicRouting from "./Practices/DynamicRouting.jsx";
import Counter from "./Practices/redux/Counter.jsx";
import { store } from "./Practices/redux/store.js";
import { Provider } from "react-redux";

export default function App() {
  return (
    <>
      <Provider store={store}>
        <BrowserRouter>
          <Routes>
            <Route path="/" element={<ReactRouter />} />
            <Route path="/Component" element={<Component />} />
            <Route
              path="/Probs"
              element={<Props name="Syed Ibrahim Ali" age={22} />}
            />
            <Route path="/EventHandling" element={<EventHandling />} />
            <Route
              path="/ConditionalRendering"
              element={<ConditionalRendering />}
            />
            <Route path="/Stopwatch" element={<Stopwatch />} />
            <Route path="/WeatherFetcher" element={<WeatherFetcher />} />
            <Route path="/DynamicRouting/:id" element={<DynamicRouting />} />
            <Route path="/Counter" element={<Counter />} />
          </Routes>
        </BrowserRouter>
      </Provider>
    </>
  );
}
