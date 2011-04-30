import processing.core.*; 
import processing.xml.*; 

import java.applet.*; 
import java.awt.Dimension; 
import java.awt.Frame; 
import java.awt.event.MouseEvent; 
import java.awt.event.KeyEvent; 
import java.awt.event.FocusEvent; 
import java.awt.Image; 
import java.io.*; 
import java.net.*; 
import java.text.*; 
import java.util.*; 
import java.util.zip.*; 
import java.util.regex.*; 

public class theme extends PApplet {

PGraphics pickbuffer = null;
int numThemes = 0;
String[] labels;
String[][] values;
Data[] dataValues;

Graph graph;

public void setup() {

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

public void draw() {
 background(255);
  graph.drawGraph();
}

public void mouseMoved() {

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

class Graph {

  float startX = width*.05f;
  float endX = width- (width*.05f);
  float startY = height*.05f;
  float endY = height - (height*.3f);
  Data[] data;
  Theme[] themes;
  int numThemes;
  String[] labels;
  float maxVal;
  float[] startingVals;

  float Yspacing;
  float Xinterval;

  public Graph(float[] _startingVals, float _max, Data[] _data, String[] _labels) {
    maxVal = _max;
    startingVals = _startingVals;
    data = _data;
    labels = _labels;
    Yspacing = ((endY*.9f)-startY)/maxVal;
    Xinterval = (endX-startX)/(labels.length-2);
    themes = new Theme[data.length];
    numThemes = data.length;
  }

  private void setupGraph() {

    float[] values;
    values = new float[startingVals.length];

    for(int i=0; i<startingVals.length; i++) {
      values[i] = startingVals[i];
    }

    themes[0] = new Theme(0, data[0], startingVals, startX, endX, endY, Yspacing, Xinterval);
    int r = (int)random(0, 255);
    int g = (int)random(0, 255);
    int b = (int)random(0, 255);
    themes[0].setColor (r, g, b);

    for(int j = 0; j< data.length-1; j++) {

      float[] temp_values = new float[values.length];

      for(int i=0; i<values.length; i++) {
        temp_values[i] = values[i];
      }

      for(int i=0; i<temp_values.length; i++) {

        float currentVal =data[j].values[i];
        temp_values[i] -= currentVal;
        values[i] -= currentVal;
      }
      themes[j+1] = new Theme(j+1, data[j+1], temp_values, startX, endX, endY, Yspacing, Xinterval);
      int r2 = (int)random(0, 255);
      int g2 = (int)random(0, 255);
      int b2 = (int)random(0, 255);
      themes[j+1].setColor (r2, g2, b2);
    }
  }


  private void drawIntervals() {
    
    float i = startX;
    int k = 1;
    while(i <= endX) {
     stroke(0);
     strokeWeight(3);
       line(i, endY+10, i, endY+20);
     
       fill(0);
       textSize(10);
       textAlign(CENTER,TOP);
       text(labels[k++], i, endY+25);
         i += Xinterval; 
    }
    
    strokeWeight(1);
    stroke(0);
  }

  private int findNearestInterval(int mousex) {
        
    float i = startX;
    int k = 0;
    while(i <= endX) {
         if((mousex > (i-Xinterval/2)) && (mousex <= (i+Xinterval/2))) return k;
         i += Xinterval; 
         k++;
    }
    return k;
  }


  private void drawGraph() {
    rectMode(CORNERS);
/*    fill(200);
    rect(startX,startY,endX,endY);*/
    drawIntervals();
    for(int i = 0; i< numThemes; i++) {  
      if (themes[i].getSelected() == true) {
        themes[i].renderSelected();
        int loc = findNearestInterval(mouseX);
        themes[i].drawLabel(loc, labels[loc+1]);
      }
      else {
        themes[i].render();
      }
    }
  }



  private void drawPickBuffer() {
    pickbuffer.beginDraw();
    pickbuffer.background(255);
    for (int i=0; i<numThemes; i++) { 
      themes[i].renderIsect(pickbuffer);
    }
    pickbuffer.endDraw();
  }

  private void checkThemes() {

    drawPickBuffer();
    int isectColor = pickbuffer.get(mouseX,mouseY);
    for (int i=0; i<numThemes; i++) { 
      if (themes[i].isect((int)red(isectColor), (int)green(isectColor), (int)blue(isectColor)) == true) {
        themes[i].setSelected(true);
      }
      else {
        themes[i].setSelected(false);
      }
    }
  }
}



class Theme {

  int id;
  Data data;
  float[] values;
  boolean selected = false;
  int r, g, b;

  float startX;
  float endX;
  float baseline;
  float Yspacing;
  float Xinterval;

  private Theme(int _id, Data _data, float[] _values, float _startX, float _endX, 
  float _base, float _spacing, float _interval) {
    id = _id;
    data = _data;
    values = _values; 
    startX = _startX;
    endX = _endX;
    baseline = _base;
    Yspacing = _spacing;
    Xinterval = _interval;
  }

  private void drawTheme() { 
    beginShape();
    vertex(startX, baseline);
    vertex(startX,  baseline-values[0]*Yspacing);
    float q = startX;
    curveVertex(q, baseline-values[0]*Yspacing);
    for(int i=0; i<values.length; i++) {
      curveVertex(q,baseline-values[i]*Yspacing); 
      q += Xinterval;
    }
    int l2 = values.length-1;
    curveVertex(endX,  baseline-values[l2]*Yspacing);
    vertex(endX,  baseline-values[l2]*Yspacing);
    vertex(endX, baseline);
    endShape(CLOSE);
  }

  private void drawLabel(int index, String time) {
  
    textAlign(CENTER,BOTTOM);
    textSize(40);
    fill(0);
    text(data.name + ", " + time + ", " + PApplet.parseInt(data.values[index]), ((endX-startX)/2) + startX, height-(height*.07f));

  }
  
  

  private void setColor(int _r, int _g, int _b) {
    r = _r; 
    g = _g; 
    b = _b;
  }

  public boolean getSelected () {
    return selected;
  }

  public void setSelected (boolean _selected) {
    selected = _selected;
  }  

  private void render() {
    fill(r,g,b);
    drawTheme();
  }

  private void renderSelected() {
    fill(r+50,g+50,b+50);
    drawTheme();
  }  

  public void renderIsect(PGraphics pg) {
    pg.fill(red(id), green(id), blue(id));
    pg.beginShape();
    pg.vertex(startX, baseline);
    pg.vertex(startX,  baseline-values[0]*Yspacing);
    float q = startX;
    pg.curveVertex(q, baseline-values[0]*Yspacing);
    for(int i=0; i<values.length; i++) {
      pg.curveVertex(q,baseline-values[i]*Yspacing); 
      q += Xinterval;
    }
    int l2 = values.length-1;
    pg.curveVertex(endX,  baseline-values[l2]*Yspacing);
    pg.vertex(endX,  baseline-values[l2]*Yspacing);
    pg.vertex(endX, baseline);
    pg.endShape(CLOSE);
  }

  public boolean isect (int _r, int _g, int _b) {
    if ((red(id) == _r) && (green(id) == _g) && (blue(id) == _b)) {
      return true;
    }
    return false;
  }
}

  static public void main(String args[]) {
    PApplet.main(new String[] { "--bgcolor=#DFDFDF", "theme" });
  }
}
