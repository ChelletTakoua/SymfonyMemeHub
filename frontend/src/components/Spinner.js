import React from "react";
import { ThreeDots } from "react-loader-spinner";

export default function Spinner({
  width = 80,
  height = 80,
  color = "#4fa94d",
}) {
  return (
    <ThreeDots
      visible={true}
      height={height}
      width={width}
      color={color}
      radius="9"
      ariaLabel="three-dots-loading"
      wrapperStyle={{}}
      wrapperClass=""
    />
  );
}
