import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import LivePie from './components/LivePie';
import CurrentlyTracking from './components/CurrentlyTracking';
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes React and other helpers. It's a great starting point while
 * building robust, powerful web applications using React + Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh React component instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// require('./components/Example');
// require('./components/CurrentlyTracking');



// console.log(chartData);
// var myChart = ReactDOM.render(<Example data={chartData}/>, document.getElementById('example'));
// console.log(myChart);

var myCurrentlyTracking = ReactDOM.render(<CurrentlyTracking/>, document.getElementById('currentlyTracking'));

var dailyPie = ReactDOM.render(<LivePie range="daily" />, document.getElementById('dailyPie'));
var weeklyPie = ReactDOM.render(<LivePie range="weekly" />, document.getElementById('weeklyPie'));
var allPie = ReactDOM.render(<LivePie range="alltime" />, document.getElementById('allPie'));