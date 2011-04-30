PGraphics pickbuffer = null;
int numThemes = 0;
String[] labels;
String[][] values;
Data[] dataValues;

Graph graph;

void setup() {

  size(900,400);
  background(255);
  pickbuffer = createGraphics(width, height, JAVA2D);
  smooth();

  String[] input = loadStrings("data.csv");
  numThemes = input.length-1;
  values = new String[numThemes][];

  for (int i = 0; i< input.length; i++) {
    if (i == 0) labels = split(input[i], ',');    
    else values[i-1] = split(input[i], ',');
  }

  dataValues = new Data [numThemes];


  float[] totalVals;

  totalVals = new float[values[0].length-1];


  for (int i = 0; i< numThemes; i++) {

    String name = values[i][0];   
    float[] themeValues;
    themeValues = new float[values[i].length-1];
    int k = 0;

    for(int j = 1; j< values[i].length; j++) {
      float val = Float.parseFloat(values[i][j]);
      themeValues[k++] = val;
      totalVals[j-1] += val;
    }

    dataValues[i] = new Data(name, themeValues);
  }

  float maxVal = totalVals[0];
  for(int p = 0; p<totalVals.length; p++) {
    if (totalVals[p] > maxVal) maxVal = totalVals[p];
  }

  graph = new Graph(totalVals, maxVal, dataValues,labels);
  graph.setupGraph();
}

void draw() {
 background(255);
  graph.drawGraph();
}

void mouseMoved() {

  graph.checkThemes();
}


class Data {
  boolean selected = false;  
  float[] values;
  String name;

  public Data(String _name, float[] _value) {
    values = _value;
    name = _name;
  }
}

