import React, { Component } from 'react';

const PieChart = require("react-chartjs").Pie;

class LivePie extends React.Component {
    constructor(props) {
        super(props);
        this.state = {data: {}};
    }

    componentDidMount() {
        this.timerID = setTimeout(()=>this.update(), 1000);
    }

    componentWillUnmount() {
      clearTimeout(this.timerID);
    }

    /**
     * Return array of percentages
     * @param  {[type]} array [description]
     * @return {[type]}       [description]
     */
    arrayPercent(array)
    {
     var i, max=0;
     var newarray = new Array();

     for (i=0; i<array.length; i++)
      if (array[i] > max) max = array[i];

     for (i=0; i<array.length; i++)
      newarray[i] = array[i] * 100 / max;

     return newarray;
    }


    update() {
        var self = this;

        // do an ajax request to the api and get the updated currently tracking
        // Make a request for a user with a given ID
        axios.get('/api/totals/' + this.props.range)
          .then(function (response) {
              let taskTotals = response.data.data;
              let chartData = [];
              let max =0;

              for ( let task in taskTotals ) {
                  chartData.push({label: task, value: taskTotals[task].length});

                  max = max + taskTotals[task].length;
              }

              // Make them percentages
              for ( let i = 0; i < chartData.length; ++i) {
                  chartData[i].value = Math.round(chartData[i].value * 100 / max);
              }
              console.log("AFTER", chartData);

              self.setState({
               data: chartData
              });

              self.timerID = setTimeout(()=>self.update(), 10000);
          })
          .catch(function (error) {


            self.timerID = setTimeout(()=>self.update(), 20000);
          });


    }

    render() {
        return (
            <PieChart data={this.state.data} width={200} height={200}  options={{animateRotate: true, labels: ['test 1', 'ok what', 'this working']}}/>
        )
    }
}

export default LivePie;