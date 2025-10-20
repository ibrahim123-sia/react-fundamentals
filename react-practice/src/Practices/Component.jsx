import React, { useState } from "react";

const Component = () => {
  const [count, setCount] = useState(0);

  return (
    <div>
      <div className="flex justify-center items-center flex-col gap-5 mt-40">
        <p className="">Count {count}</p>
        <button
          className="rounded-lg bg-blue-700 px-20 py-5 text-white "
          onClick={() => setCount(count + 1)}
        >
          Click
        </button>
      </div>
    </div>
  );
};

export default Component;
