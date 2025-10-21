import React from 'react'
import { useParams } from 'react-router-dom'
const DynamicRouting = () => {
    const {id} = useParams();

  return (
    <div>
        <h1>Dynamic Routing Page</h1>
        <h2>The ID you have entered is: {id}</h2>
    </div>
  )
}

export default DynamicRouting
