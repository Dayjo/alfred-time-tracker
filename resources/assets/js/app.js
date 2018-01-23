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


// The currently tracking bar
if ( document.getElementById('currentlyTracking') ) {
    var myCurrentlyTracking = ReactDOM.render(<CurrentlyTracking/>, document.getElementById('currentlyTracking'));
}


// Find any pie charts and load them up
var pies = document.querySelectorAll('.LivePie');
var reactPies = [];
for ( var i = 0; i < pies.length; ++i ) {
    let pieRange = pies[i].getAttribute('data-range');
    let width = pies[i].getAttribute('data-width');
    reactPies.push( ReactDOM.render(<LivePie range={pieRange} width={width}/>, pies[i]) );
}

// The event listenres for the report form
document.getElementById('reportRangeForm').addEventListener('submit', function(e){
    e.preventDefault();
    var from = document.getElementById('reportFrom').value;
    var to = document.getElementById('reportTo').value;
    document.location.href = "/report/range/" + from + "/" + to;
});