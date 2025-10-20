import React from "react";
import { Route, Routes, BrowserRouter } from "react-router-dom";
import Component from "../src/Practices/Component.jsx";
import Props from "../src/Practices/Props.jsx";
import EventHandling from "../src/Practices/EventHandling.jsx";
import ConditionalRendering from "../src/Practices/ConditionalRendering.jsx";
import Stopwatch from "../src/Practices/Stopwatch.jsx";
export default function App() {
  return (
    <>
      <BrowserRouter>
        <Routes>
          {/* <Route path="/" element={<Component />} /> */}
          {/* <Route path="/" element={<Props name='Syed Ibrahim Ali' age={22}/>}/> */}
          {/* <Route path="/" element={<EventHandling />} /> */}
          {/* <Route path="/" element={<ConditionalRendering/>}/> */}
          <Route path="/" element={<Stopwatch/>}/>
        </Routes>
      </BrowserRouter>
    </>
  );
}
