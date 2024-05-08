import React, { useEffect, useState } from "react";
import Card from "../components/Card";
import BackToTop from "../components/BackToTopButton";
import { memeApi } from "../services/api";
import { FaArrowAltCircleUp } from "react-icons/fa";

const Home = () => {
  const [memes, setMemes] = useState([]);
  const [memesPerPage, setMemesPerPage] = useState(10);
  const [page, setPage] = useState(1);
  const [sortOrder, setSortOrder] = useState("desc");
  const [numberOfPages, setNumberOfPages] = useState(0);

  useEffect(() => {
    const fetchMemes = async () => {
      try {
        const res = await memeApi.getAllMemes(page, memesPerPage, sortOrder);
        console.log(res.data);
        setNumberOfPages(res?.data.totalPages);
        setMemes(res?.data.memes);
      } catch (error) {
        console.error("Error fetching data:", error);
      }
    };
    fetchMemes();
  }, [memesPerPage, page, sortOrder]);

  return (
    <div className="grow bg-palenight">
      <header className="text-center py-20 bg-gradient-to-r from-greens-200 to-palenight shadow-2xl text-white">
        <h1 className="text-5xl mb-5 animate-ping-once ">
          Welcome to MemeHub!
        </h1>
        <p className="text-xl animate-pulse ">
          Enjoy the best memes from around the world
        </p>
      </header>
      <BackToTop />
      <div className="controls flex justify-center space-x-4 my-4">
        <div className="relative inline-flex">
          <svg
            className="w-2 h-2 absolute top-0 right-0 m-4 pointer-events-none"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 412 232"
          >
            <path
              d="M206 171.144L42.678 7.822c-9.763-9.763-25.592-9.763-35.355 0-9.763 9.762-9.763 25.592 0 35.355l189.21 189.211c9.372 9.373 24.749 9.373 34.121 0l189.211-189.211c9.763-9.763 9.763-25.592 0-35.355-9.763-9.763-25.592-9.763-35.355 0L206 171.144z"
              fill="#648299"
              fillRule="nonzero"
            />
          </svg>
          <select
            value={memesPerPage}
            onChange={(e) => setMemesPerPage(e.target.value)}
            className="border border-gray-300 rounded-full text-gray-600 h-10 pl-5 pr-10 bg-white hover:border-gray-400 focus:outline-none appearance-none"
          >
            <option value={10}>Show 10</option>
            <option value={20}>Show 20</option>
            <option value={50}>Show 50</option>
          </select>
        </div>
        <div className="relative inline-flex">
          <svg
            className="w-2 h-2 absolute top-0 right-0 m-4 pointer-events-none"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 412 232"
          >
            <path
              d="M206 171.144L42.678 7.822c-9.763-9.763-25.592-9.763-35.355 0-9.763 9.762-9.763 25.592 0 35.355l189.21 189.211c9.372 9.373 24.749 9.373 34.121 0l189.211-189.211c9.763-9.763 9.763-25.592 0-35.355-9.763-9.763-25.592-9.763-35.355 0L206 171.144z"
              fill="#648299"
              fillRule="nonzero"
            />
          </svg>
          <select
            value={sortOrder}
            onChange={(e) => setSortOrder(e.target.value)}
            className="border border-gray-300 rounded-full text-gray-600 h-10 pl-5 pr-10 bg-white hover:border-gray-400 focus:outline-none appearance-none"
          >
            <option value="asc">Sort ↑</option>
            <option value="desc">Sort ↓</option>
          </select>
        </div>
      </div>
      <div className="grid grid-cols-2 mt-10 gap-y-12 gap-x-24  w-2/3 m-auto">
        {memes?.map((meme) => (
          <Card key={meme.id} meme={meme} />
        ))}
      </div>
      <div className="flex justify-center space-x-4 my-4">
        <button
          onClick={() => setPage(page - 1)}
          disabled={page === 1}
          className={`${
            page === 1 ? "bg-gray-800 text-white" : "bg-white text-gray-800"
          } px-3 py-1 rounded-full`}
        >
          ←
        </button>
        {Array.from({ length: numberOfPages }, (_, i) => i + 1).map((num) => (
          <button
            key={num}
            onClick={() => setPage(num)}
            className={`${
              num === page ? "bg-gray-800 text-white" : "bg-white text-gray-800"
            } px-3 py-1 rounded-full`}
          >
            {num}
          </button>
        ))}
        <button
          onClick={() => setPage(page + 1)}
          disabled={page === numberOfPages}
          className={`${
            page === numberOfPages
              ? "bg-gray-800 text-white"
              : "bg-white text-gray-800"
          } px-3 py-1 rounded-full`}
        >
          →
        </button>
      </div>
    </div>
  );
};

export default Home;
