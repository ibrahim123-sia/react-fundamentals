import React from "react";
import { Link } from "react-router-dom";
import Component from "./Component";
import Props from "./Props";
import EventHandling from "./EventHandling";
import ConditionalRendering from "./ConditionalRendering";
import Stopwatch from "./Stopwatch";
import WeatherFetcher from "./weatherFetcher";
import DynamicRouting from "./DynamicRouting";
const ReactRouter = () => {
  return (
    <div className="justify-center items-center">
      <h1>React Router Practice</h1>
      <nav>
        <ul>
          <li>
            <Link to="/Component">Component</Link>
          </li>
          <li>
            <Link to="/Probs">Props</Link>
          </li>
          <li>
            <Link to="/EventHandling">Event Handling</Link>
          </li>
          <li>
            <Link to="/ConditionalRendering">Conditional Rendering</Link>
          </li>
          <li>
            <Link to="/Stopwatch">Stopwatch</Link>
          </li>
          <li>
            <Link to="/WeatherFetcher">Weather Fetcher</Link>
          </li>
          <li>
            <Link to="/DynamicRouting">Dynamic Routing</Link>
          </li>
          <li><Link to='/Counter'>Counter</Link></li>
        </ul>
      </nav>
    </div>
  );
};

export default ReactRouter;
